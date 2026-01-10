document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- ХЕЛПЕРЫ ---

    // Функция отправки
    async function sendRequest(url, formData) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await response.json();
            return { ok: response.ok, data: data, status: response.status };
        } catch (error) {
            console.error('Error:', error);
            return { ok: false, data: { message: 'Ошибка сети. Попробуйте позже.' } };
        }
    }

    // Функция вывода результата в <div class="result">
    function showResult(form, type, content) {
        const resultDiv = form.querySelector('.result');
        if (!resultDiv) return;

        resultDiv.innerHTML = ''; // Очистка

        if (type === 'success') {
            resultDiv.innerHTML = `<div class="message-success">${content}</div>`;
        } else {
            // Обработка ошибок (строка или объект Laravel)
            let errorHtml = '';
            if (typeof content === 'object') {
                errorHtml = '<ul>';
                for (const field in content) {
                    content[field].forEach(err => {
                        errorHtml += `<li>${err}</li>`;
                    });
                }
                errorHtml += '</ul>';
            } else {
                errorHtml = content;
            }
            resultDiv.innerHTML = `<div class="message-error">${errorHtml}</div>`;
        }
    }

    // --- ОБРАБОТЧИКИ ФОРМ ---

    // 1. Вход (Login)
    const loginForm = document.querySelector('.modal[data-modal-name="login"] form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const resultDiv = this.querySelector('.result');
            if(resultDiv) resultDiv.innerHTML = 'Загрузка...';

            // Используем FormData напрямую из формы - это безопаснее
            const formData = new FormData(this);
            
            // Получаем значения из FormData для проверки
            const email = formData.get('email');
            const password = formData.get('password');

            // Проверяем, что поля заполнены
            if (!email || !email.toString().trim() || !password || !password.toString().trim()) {
                showResult(this, 'error', 'Заполните все обязательные поля');
                return;
            }

            // Создаем новый FormData с очищенными значениями
            const cleanFormData = new FormData();
            cleanFormData.append('email', email.toString().trim());
            cleanFormData.append('password', password.toString());

            const result = await sendRequest('/ajax/login', cleanFormData);

            if (result.ok) {
                showResult(this, 'success', 'Вход выполнен! Перенаправление...');
                
                // Закрываем модальное окно
                const loginModal = document.querySelector('.js-modal[data-modal-name="login"]');
                if (loginModal) {
                    loginModal.classList.remove('is-open');
                }
                document.body.classList.remove('no-scroll');
                
                // Редирект на страницу профиля
                if (result.data.redirect) {
                    window.location.href = result.data.redirect;
                } else {
                    window.location.href = '/profile';
                }
            } else {
                // Если есть errors от валидации Laravel, берем их, иначе message
                const msg = result.data.errors || result.data.message;
                showResult(this, 'error', msg);
            }
        });
    }

    // 2. Регистрация (Register)
    const registerForm = document.querySelector('.modal[data-modal-name="sign-up"] form');
    const regBtn = document.getElementById('sign-up-submit-btn');
    
    if (registerForm && regBtn) {
        regBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation(); // Останавливаем всплытие события
            handleRegister(registerForm);
        });
    }

    async function handleRegister(form) {
        const resultDiv = form.querySelector('.result');
        if(resultDiv) resultDiv.innerHTML = 'Загрузка...';

        const nameField = document.getElementById('sign-up-name');
        const emailField = document.getElementById('sign-up-phone');
        const passwordField = document.getElementById('sign-up-password');

        // Проверка заполненности полей
        if (!nameField || !nameField.value.trim()) {
            showResult(form, 'error', 'Введите ваше имя');
            return;
        }
        if (!emailField || !emailField.value.trim()) {
            showResult(form, 'error', 'Введите email');
            return;
        }
        if (!passwordField || !passwordField.value.trim()) {
            showResult(form, 'error', 'Введите пароль');
            return;
        }

        const formData = new FormData();
        formData.append('name', nameField.value.trim());
        formData.append('email', emailField.value.trim());
        formData.append('password', passwordField.value);

        // Чекбокс согласия
        const agreementCheckbox = document.getElementById('sign-up-agreement');
        if (agreementCheckbox && agreementCheckbox.checked) {
            formData.append('agreement', 'yes');
        }

        const result = await sendRequest('/ajax/register', formData);

        if (result.ok) {
            // 1. Очищаем форму
            form.reset();
            if(resultDiv) resultDiv.innerHTML = ''; 

            // 2. Закрываем все модальные окна
            const signUpModal = document.querySelector('.js-modal[data-modal-name="sign-up"]');
            if (signUpModal) {
                signUpModal.classList.remove('is-open');
            }
            
            // 3. Разблокируем прокрутку
            document.body.classList.remove('no-scroll');

            // 4. Редирект на страницу профиля
            if (result.data.redirect) {
                window.location.href = result.data.redirect;
            } else {
                // Если redirect не пришел, используем стандартный путь
                window.location.href = '/profile';
            }

        } else {
            const msg = result.data.errors || result.data.message;
            showResult(form, 'error', msg);
        }
    }

    // Обработчики для показа/скрытия паролей
    const displayPasswordCheckboxes = document.querySelectorAll('input[type="checkbox"][id*="display-password"]');
    displayPasswordCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Для login-display-password ищем login-password
            // Для general-info-display-password ищем general-info-password
            let passwordFieldId = this.id.replace('display-', '');
            
            // Если замена не дала результата, пробуем другой паттерн
            const passwordField = document.getElementById(passwordFieldId);
            if (!passwordField) {
                // Пытаемся найти поле пароля рядом с чекбоксом
                const form = this.closest('form');
                if (form) {
                    const passwordFields = form.querySelectorAll('input[type="password"]');
                    // Берем первое найденное поле пароля в форме
                    if (passwordFields.length > 0) {
                        passwordFields[0].type = this.checked ? 'text' : 'password';
                        return;
                    }
                }
            } else {
                passwordField.type = this.checked ? 'text' : 'password';
            }
        });
    });

    // 4. Выход из аккаунта
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const formData = new FormData();
            const result = await sendRequest('/ajax/logout', formData);
            
            if (result.ok) {
                // Редирект на главную страницу
                if (result.data.redirect) {
                    window.location.href = result.data.redirect;
                } else {
                    window.location.href = '/';
                }
            } else {
                // Если произошла ошибка, все равно перенаправляем на главную
                window.location.href = '/';
            }
        });
    }

    // 3. Восстановление пароля
    const recoveryForm = document.querySelector('.modal[data-modal-name="password-recovery"] form');
    if (recoveryForm) {
        const recoverBtn = recoveryForm.querySelector('button.btn--primary');
        // Проверяем тип кнопки, чтобы не дублировать события
        const eventType = recoverBtn.type === 'submit' ? 'submit' : 'click';
        const target = recoverBtn.type === 'submit' ? recoveryForm : recoverBtn;

        target.addEventListener(eventType, async function(e) {
            e.preventDefault();
            const resultDiv = recoveryForm.querySelector('.result');
            if(resultDiv) resultDiv.innerHTML = 'Отправка...';
            
            // Скрываем стандартное сообщение успеха верстки, будем использовать .result
            const staticSuccess = recoveryForm.querySelector('.success-message');
            if(staticSuccess) staticSuccess.style.display = 'none';

            const emailInput = document.getElementById('recovery-phone');
            const formData = new FormData();
            formData.append('email', emailInput.value);

            const result = await sendRequest('/ajax/password/email', formData);

            if (result.ok) {
                showResult(recoveryForm, 'success', result.data.message || 'Ссылка отправлена на почту');
            } else {
                const msg = result.data.errors || result.data.message;
                showResult(recoveryForm, 'error', msg);
            }
        });
    }

    // 5. Обновление профиля
    const generalInfoForm = document.getElementById('general-info-form');
    if (generalInfoForm) {
        generalInfoForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const resultDiv = this.querySelector('.result');
            if(resultDiv) resultDiv.innerHTML = 'Загрузка...';

            const formData = new FormData(this);
            
            // Получаем значения из FormData для проверки
            const password = formData.get('password');
            const passwordConfirmation = formData.get('password_confirmation');
            const name = formData.get('name');

            // Проверяем, что если пароль заполнен, то и подтверждение тоже
            if (password && password.trim() && (!passwordConfirmation || !passwordConfirmation.trim())) {
                showResult(this, 'error', 'Подтвердите пароль');
                return;
            }

            // Проверяем совпадение паролей
            if (password && password.trim() && password !== passwordConfirmation) {
                showResult(this, 'error', 'Пароли не совпадают');
                return;
            }

            // Минимальная длина пароля
            if (password && password.trim() && password.length < 6) {
                showResult(this, 'error', 'Пароль должен содержать минимум 6 символов');
                return;
            }

            // Если пароль не заполнен, удаляем его из FormData
            if (!password || !password.trim()) {
                formData.delete('password');
                formData.delete('password_confirmation');
            }

            const result = await sendRequest('/profile/update', formData);

            if (result.ok) {
                showResult(this, 'success', result.data.message || 'Данные успешно обновлены');
                
                // Обновляем данные на странице без перезагрузки (если имя изменено)
                if (name && name.trim()) {
                    const nameDisplay = document.querySelector('.profile__point:has(.profile__point-title:contains("Имя")) .profile__point-text');
                    if (nameDisplay) {
                        nameDisplay.textContent = name.trim();
                    }
                }

                // Очищаем поля пароля после успешного сохранения
                const passwordFields = this.querySelectorAll('input[type="password"]');
                passwordFields.forEach(field => {
                    field.value = '';
                });

                // Скрываем сообщение через 3 секунды
                setTimeout(() => {
                    if(resultDiv) resultDiv.innerHTML = '';
                }, 3000);

            } else {
                const msg = result.data.errors || result.data.message;
                showResult(this, 'error', msg);
            }
        });
    }
});