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
    // Ищем все чекбоксы для показа пароля (они содержат "display" в ID)
    const displayPasswordCheckboxes = document.querySelectorAll('input[type="checkbox"][id*="display"]');
    
    displayPasswordCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Сначала пытаемся найти поле по ID (заменяем "display-" на пустую строку)
            // Например: general-info-display-password -> general-info-password
            // Например: general-info-display-repeat-password -> general-info-repeat-password
            let passwordFieldId = this.id.replace('display-', '');
            
            // Пробуем найти по ID
            let passwordField = document.getElementById(passwordFieldId);
            
            // Если не нашли по ID, ищем поле пароля в том же блоке .form-password
            if (!passwordField) {
                const formPasswordDiv = this.closest('.form-password');
                if (formPasswordDiv) {
                    // Ищем поле пароля в том же блоке (input с type="password" или type="text")
                    passwordField = formPasswordDiv.querySelector('input[name="password"], input[name="password_confirmation"], input[name="current_password"], input[type="password"], input[type="text"]');
                }
            }
            
            // Если нашли поле, меняем его тип
            if (passwordField) {
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
        recoveryForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const resultDiv = this.querySelector('.result');
            if(resultDiv) resultDiv.innerHTML = 'Отправка...';

            const emailInput = document.getElementById('recovery-email');
            
            // Проверка заполненности поля
            if (!emailInput || !emailInput.value.trim()) {
                showResult(this, 'error', 'Введите email');
                return;
            }

            const formData = new FormData();
            formData.append('email', emailInput.value.trim());

            const result = await sendRequest('/ajax/recover-password', formData);

            if (result.ok) {
                showResult(this, 'success', result.data.message || 'Новый пароль отправлен на указанный email');
                // Очищаем форму после успешной отправки
                this.reset();
                
                // Опционально: закрываем модальное окно через 3 секунды
                setTimeout(() => {
                    const recoveryModal = document.querySelector('.js-modal[data-modal-name="password-recovery"]');
                    if (recoveryModal) {
                        recoveryModal.classList.remove('is-open');
                    }
                    document.body.classList.remove('no-scroll');
                }, 3000);
            } else {
                const msg = result.data.errors || result.data.message;
                showResult(this, 'error', msg);
            }
        });
    }

    // 5. Обновление профиля
    const generalInfoForm = document.getElementById('general-info-form');
    if (generalInfoForm) {
        // Очищаем результат и отменяем timeout при открытии модального окна
        const generalInfoModal = document.querySelector('.js-modal[data-modal-name="general-info"]');
        if (generalInfoModal) {
            // Используем MutationObserver для отслеживания открытия модального окна
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (generalInfoModal.classList.contains('is-open')) {
                            // Модальное окно открыто - очищаем предыдущие состояния
                            // Отменяем timeout закрытия, если он был установлен
                            if (generalInfoForm.dataset.closeTimeout) {
                                clearTimeout(parseInt(generalInfoForm.dataset.closeTimeout));
                                generalInfoForm.dataset.closeTimeout = '';
                            }
                            
                            // Очищаем предыдущий результат
                            const resultDiv = generalInfoForm.querySelector('.result');
                            if (resultDiv) {
                                resultDiv.innerHTML = '';
                            }
                            // Убеждаемся, что форма не в состоянии отправки
                            generalInfoForm.dataset.submitting = 'false';
                        }
                    }
                });
            });
            
            // Начинаем наблюдение за изменениями класса
            observer.observe(generalInfoModal, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        generalInfoForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            e.stopPropagation(); // Предотвращаем всплытие события
            
            // Проверяем, не отправляется ли форма уже
            if (this.dataset.submitting === 'true') {
                return;
            }
            this.dataset.submitting = 'true';

            const resultDiv = this.querySelector('.result');
            if(resultDiv) resultDiv.innerHTML = 'Загрузка...';

            const formData = new FormData(this);
            
            // Получаем значения из FormData для проверки
            const password = formData.get('password');
            const passwordConfirmation = formData.get('password_confirmation');
            const name = formData.get('name');

            // Проверяем, что если пароль заполнен, то и подтверждение тоже
            if (password && password.trim() && (!passwordConfirmation || !passwordConfirmation.trim())) {
                this.dataset.submitting = 'false';
                showResult(this, 'error', 'Подтвердите пароль');
                return;
            }

            // Проверяем совпадение паролей
            if (password && password.trim() && password !== passwordConfirmation) {
                this.dataset.submitting = 'false';
                showResult(this, 'error', 'Пароли не совпадают');
                return;
            }

            // Минимальная длина пароля
            if (password && password.trim() && password.length < 6) {
                this.dataset.submitting = 'false';
                showResult(this, 'error', 'Пароль должен содержать минимум 6 символов');
                return;
            }

            // Если пароль не заполнен, удаляем его из FormData
            if (!password || !password.trim()) {
                formData.delete('password');
                formData.delete('password_confirmation');
            }

            const result = await sendRequest('/profile/update', formData);
            
            // Сбрасываем флаг отправки после получения ответа
            this.dataset.submitting = 'false';

            if (result.ok) {
                showResult(this, 'success', result.data.message || 'Данные успешно обновлены');
                
                // Обновляем данные на странице без перезагрузки
                const changedFields = result.data.changed_fields || [];
                
                // Получаем новые значения из формы
                const newName = name && name.trim() ? name.trim() : null;
                const newPhone = formData.get('phone') && formData.get('phone').trim() ? formData.get('phone').trim() : null;
                
                // Обновляем имя, если оно было изменено
                if (changedFields.includes('name') && newName) {
                    const profileList = document.querySelector('.profile__list.general');
                    if (profileList) {
                        const namePoints = profileList.querySelectorAll('.profile__point');
                        namePoints.forEach(point => {
                            const title = point.querySelector('.profile__point-title');
                            if (title && title.textContent.trim() === 'Имя') {
                                const textElement = point.querySelector('.profile__point-text');
                                if (textElement) {
                                    textElement.textContent = newName;
                                }
                            }
                        });
                    }
                }

                // Обновляем телефон, если он был изменен
                if (changedFields.includes('phone')) {
                    const profileList = document.querySelector('.profile__list.general');
                    if (profileList) {
                        // Ищем существующий блок с телефоном
                        let phonePoint = null;
                        const allPoints = profileList.querySelectorAll('.profile__point');
                        allPoints.forEach(point => {
                            const title = point.querySelector('.profile__point-title');
                            if (title && title.textContent.trim() === 'Номер телефона') {
                                phonePoint = point;
                            }
                        });
                        
                        if (phonePoint) {
                            // Обновляем существующий телефон
                            const textElement = phonePoint.querySelector('.profile__point-text');
                            if (textElement) {
                                if (newPhone) {
                                    textElement.textContent = newPhone;
                                } else {
                                    // Если телефон был удален, удаляем блок
                                    phonePoint.remove();
                                }
                            }
                        } else if (newPhone) {
                            // Если блока с телефоном нет, но телефон был добавлен, создаем его
                            // Телефон должен быть ПЕРВЫМ в списке (перед Email)
                            const firstPoint = profileList.querySelector('.profile__point');
                            if (firstPoint && firstPoint.parentNode) {
                                const phoneDiv = document.createElement('div');
                                phoneDiv.className = 'profile__point';
                                phoneDiv.innerHTML = `
                                    <dt class="profile__point-title">Номер телефона</dt>
                                    <dd class="profile__point-text">${newPhone}</dd>
                                `;
                                // Вставляем в начало списка
                                profileList.insertBefore(phoneDiv, firstPoint);
                            }
                        }
                    }
                }

                // Если пароль был изменен, в профиле все равно показываем звездочки (пароль захеширован)

                // Очищаем поля пароля после успешного сохранения
                const passwordFields = this.querySelectorAll('input[type="password"], input[type="text"]');
                passwordFields.forEach(field => {
                    if (field.name === 'password' || field.name === 'password_confirmation') {
                        field.value = '';
                        // Возвращаем тип обратно в password, если был text
                        if (field.type === 'text') {
                            field.type = 'password';
                        }
                    }
                });

                // Сбрасываем чекбоксы показа пароля
                const displayCheckboxes = this.querySelectorAll('input[type="checkbox"][id*="display"]');
                displayCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Закрываем модальное окно через короткую задержку (чтобы пользователь видел сообщение об успехе)
                const closeTimeoutId = setTimeout(() => {
                    const generalInfoModal = document.querySelector('.js-modal[data-modal-name="general-info"]');
                    if (generalInfoModal && generalInfoModal.classList.contains('is-open')) {
                        // Используем стандартный механизм закрытия через кнопку закрытия,
                        // чтобы правильно обновить все переменные и состояния
                        const closeBtn = generalInfoModal.querySelector('.js-modal-close');
                        if (closeBtn) {
                            // Используем простой клик вместо dispatchEvent для надежности
                            closeBtn.click();
                        } else {
                            // Если кнопка не найдена, закрываем вручную
                            generalInfoModal.classList.remove('is-open');
                            document.body.classList.remove('no-scroll');
                        }
                    }
                    
                    // Очищаем сообщение результата и сбрасываем флаг после закрытия
                    setTimeout(() => {
                        if(resultDiv) resultDiv.innerHTML = '';
                        generalInfoForm.dataset.closeTimeout = '';
                    }, 300);
                }, 1500);
                
                // Сохраняем ID timeout для возможной отмены
                generalInfoForm.dataset.closeTimeout = closeTimeoutId;

            } else {
                const msg = result.data.errors || result.data.message;
                showResult(this, 'error', msg);
            }
        });
    }
});