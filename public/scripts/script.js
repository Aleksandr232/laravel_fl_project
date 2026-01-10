'use strict';
import Swiper from 'swiper';
import { Navigation, Keyboard } from 'swiper/modules';

window.addEventListener('DOMContentLoaded', () => {
    let isMenuOpen = false;
    let currentModalOpen = "";
    fix100vh();

    window.addEventListener('resize', () => {
        fix100vh();
        if (window.innerWidth > 768 && document.body.classList.contains('no-scroll') && !currentModalOpen) {
            document.body.classList.remove('no-scroll');
            mobileSearch.classList.remove('is-open');
            mobileCatalog.classList.remove('is-open');
        } else if ((window.innerWidth <= 768 && isMenuOpen) || !!currentModalOpen) {
            document.body.classList.add('no-scroll');
        }

        if (window.innerWidth > 480) {
            !!characterictics && characterictics.forEach((list, i) => {
                if (list.classList.contains('is-show-more-btn')) {
                    list.classList.remove('is-show-more-btn');
                    list.querySelector('.js-characteristics-wrap').style.maxHeight = 'none';
                }
            });
        } else {
            checkCharactericticsHeight();
        }

        setDisplayAccountTooltip();
    });
    
    const yandexMap = document.getElementById("yandex-map");

    const initYandexMap = () => {
        if (window.yandexMapDidInit) {
            return false;
        }
        window.yandexMapDidInit = true;

        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = true;

        script.src = 'https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Aa65fab9bb0aea1691bdb5ea14265b28b9ec1eb21b9c03e013ded3f43d39e5dc4&amp;lang=ru_RU&amp;scroll=true';

        yandexMap.appendChild(script);
    }

    const initYandexMapOnEvent = (e) => {
        initYandexMap();
        e.currentTarget.removeEventListener(e.type, initYandexMapOnEvent);
    }

    if (yandexMap) {
        setTimeout(initYandexMap, 3000);

        document.addEventListener('scroll', initYandexMapOnEvent);
        document.addEventListener('mousemove', initYandexMapOnEvent);
        document.addEventListener('touchstart', initYandexMapOnEvent);
    }

    // END Map

    // Up button
    const upBtn = document.querySelector('.js-up-btn');

    upBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
    // END Up button

    // Header submenu
    const headerSubmenuBtn = document.querySelector('.js-header-submenu-btn');
    const headerSubmenu = document.querySelector('.js-header-submenu');

    const closeSubmenu = () => {
        headerSubmenu.classList.remove('is-open');
    }

    headerSubmenuBtn.addEventListener('click', () => {
        if (headerSubmenu.classList.contains('is-open')) {
            closeSubmenu();
        } else {
            headerSubmenu.classList.add('is-open');
        }
    });
    
    // END Header submenu

    // Header menu
    const header = document.querySelector('.js-header');
    const headerMenu = document.querySelector('.js-header-menu');
    const headerMenuBtn = document.querySelector('.js-header-menu-btn');

    headerMenuBtn.addEventListener('click', () => {
        if (header.classList.contains('is-open-menu')) {
            closeMenu();
            isMenuOpen = false;
            hiddenScrollMobile(false);
        } else {
            header.classList.add('is-open-menu');
            isMenuOpen = true;
            hiddenScrollMobile(true);
        }
    });

    const closeMenu = () => {
        header.classList.remove('is-open-menu');
    }
    // END Header menu

    // Header search hint
    const headerSearch = document.querySelector('.js-header-search');
    const headerSearchInput = document.querySelector('.js-header-search-input');

    const openHint = () => {
        headerSearch.classList.add('is-open-hint');
    }

    const closeHint = () =>  {
        headerSearch.classList.remove('is-open-hint');
    }

    headerSearchInput.addEventListener('focus', () => {
        if (headerSearchInput.value.trim() != "") {
            openHint();
        }
    });

    headerSearchInput.addEventListener('input', () => {
        if (headerSearchInput.value.trim() != "") {
            openHint();
        } else if (headerSearch.classList.contains('is-open-hint')) {
            closeHint();
        }
    });

    headerSearchInput.addEventListener('blur', () => {
        if (headerSearch.classList.contains('is-open-hint')) {
            closeHint();
        }
    });

    
    // END Header search hint

    // Mobile menu btns
    const mobileMenuSearchBtn = document.querySelector('.js-mobile-menu-search-btn');
    const mobileMenuCatalogBtn = document.querySelector('.js-mobile-menu-catalog-btn');

    mobileMenuSearchBtn.addEventListener('click', () => {
        mobileSearch.classList.add('is-open');
        hiddenScrollMobile(true);
    });
    mobileMenuCatalogBtn.addEventListener('click', () => {
        mobileCatalog.classList.add('is-open');
        hiddenScrollMobile(true);
    });
    // END Mobile menu btns

    // Mobile search
    const mobileSearch = document.querySelector('.js-mobile-search');
    const closeSearchBtn = document.querySelector('.js-close-search-btn');

    closeSearchBtn.addEventListener('click', () => {
        mobileSearch.classList.remove('is-open');
        hiddenScrollMobile(false);
    });
    // END Mobile search

    // Mobile search hint
    const mobileSearchInput = document.querySelector('.js-mobile-search-input');
    const mobileSearchHint = document.querySelector('.js-mobile-search-hint');

    const openMobileHint = () => {
        mobileSearchHint.classList.add('is-open');
    }

    const closeMobileHint = () => {
        mobileSearchHint.classList.remove('is-open');
    }

    mobileSearchInput.addEventListener('focus', () => {
        if (mobileSearchInput.value.trim() != "") {
            openMobileHint();
        }
    });

    mobileSearchInput.addEventListener('input', () => {
        if (mobileSearchInput.value.trim() != "") {
            openMobileHint();
        } else if (mobileSearchHint.classList.contains('is-open')) {
            closeMobileHint();
        }
    });
    // END Mobile search hint

    // Mobile catalog
    const mobileCatalog = document.querySelector('.js-mobile-catalog');
    const closeCatalogBtn = document.querySelector('.js-close-catalog-btn');

    closeCatalogBtn.addEventListener('click', () => {
        mobileCatalog.classList.remove('is-open');
        hiddenScrollMobile(false);
    });
    // END Mobile catalog

    // Header catalog
    const headerCatalogBtn = document.querySelector('.js-header-catalog-btn');
    const headerCatalog = document.querySelector('.js-header-catalog');
    const headerCatalogMenu = document.querySelector('.js-header-catalog-menu');
    const headerCategoryItems = document.querySelectorAll('.js-header-category-item');

    const openCatalog = () => {
        headerCatalog.classList.add('is-open');
    }
    
    const closeCatalog = () => {
        headerCatalog.classList.remove('is-open');
        headerCategoryItems[0].click();
    }

    headerCategoryItems.forEach(categoryItem => {
        categoryItem.addEventListener('click', () => {
            if (!categoryItem.classList.contains('is-selected')) {
                headerCategoryItems.forEach(item => item.classList.remove('is-selected'));
                categoryItem.classList.add('is-selected');
            }
        });
    });
    headerCategoryItems[0].click();

    headerCatalogBtn.addEventListener('click', () => {
        if (!headerCatalog.classList.contains('is-open')) {
            openCatalog();
        } else {
            closeCatalog();
        }
    });
    // END Header catalog

    // Accordions
    const accordionBtns = document.querySelectorAll('.js-accordion-btn');

    accordionBtns.forEach(btn => {
        let expanded = btn.getAttribute('aria-expanded') === 'true';
        let content = btn.nextElementSibling;

        if (btn.getAttribute('aria-expanded') === 'true') {
            content.style.maxHeight = content.scrollHeight + 'px';
        }
        
        btn.addEventListener('click', () => {
            expanded = btn.getAttribute('aria-expanded') === 'true';
            content = btn.nextElementSibling;
            if (!expanded) {
                btn.setAttribute('aria-expanded', !expanded);
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                btn.setAttribute('aria-expanded', !expanded);
                content.style.maxHeight = '0';
            }
        });
    });
    // END Accordions

    // Modal
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    const modalCloseBtns = document.querySelectorAll('.js-modal-close');

    const openModal = (e) => {
        const target = e.currentTarget;
        if (!!currentModalOpen) {
            const prevModal = document.querySelector(`[data-modal-name=${currentModalOpen}]`);
            if (prevModal) {
                setTimeout(() => {
                    prevModal.classList.remove('is-open');
                    prevModal.dataset.justOpened = 'false';
                }, 200);
            }
        }
        const modalTarget = document.querySelector(`[data-modal-name=${target.dataset.modalTrigger}]`);
        if (modalTarget) {
            // Отменяем любые активные таймеры закрытия для этого модального окна
            const form = modalTarget.querySelector('#general-info-form');
            if (form && form.dataset.closeTimeout) {
                clearTimeout(parseInt(form.dataset.closeTimeout));
                form.dataset.closeTimeout = '';
            }
            
            modalTarget.classList.add('is-open');
            document.body.classList.add('no-scroll');
            currentModalOpen = target.dataset.modalTrigger;
            
            // Устанавливаем флаг, что модальное окно только что открыто
            modalTarget.dataset.justOpened = 'true';
            setTimeout(() => {
                if (modalTarget) {
                    modalTarget.dataset.justOpened = 'false';
                }
            }, 300);
        }
    }

    const closeModal = (modal) => {
        if (modal && modal.classList.contains('is-open')) {
            modal.classList.remove('is-open');
            document.body.classList.remove('no-scroll');
            currentModalOpen = "";
            modal.dataset.justOpened = 'false';
            
            // Отменяем любые активные таймеры закрытия
            const form = modal.querySelector('#general-info-form');
            if (form && form.dataset.closeTimeout) {
                clearTimeout(parseInt(form.dataset.closeTimeout));
                form.dataset.closeTimeout = '';
            }
        }
    }

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', e => openModal(e));
    })

    modalCloseBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(btn.closest('.js-modal'));
        })
    })

    
    // END Modal

    // Sliders
    const manufacturersSlider = new Swiper('.main-manufacturers-slider', {
        modules: [Navigation, Keyboard],
        slidesPerView: 2,
        spaceBetween: 16,
        keyboard: {
            enabled: true,
            onlyInViewport: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 3.1,
                spaceBetween: 24
            },
            1024: {
                slidesPerView: 4.1,
                spaceBetween: 24
            },
            1280: {
                slidesPerView: 5.2,
                spaceBetween: 40
            },
            1520: {
                slidesPerView: 6,
                spaceBetween: 40
            }
        }
    });

    const setSliderBeforeParams = () => {
        const slidersCount = manufacturersSlider.slides.length;
        const spaceBetween = manufacturersSlider.params.spaceBetween;
        const sliderWrapperLenght = (slidersCount - 1) * manufacturersSlider.slides[0].offsetWidth + spaceBetween * (slidersCount - 1);
        const sliderBeforeLeft = manufacturersSlider.slides[0].offsetWidth / 2;
        const manufacturersSliderWrapper = manufacturersSlider.slidesEl;
        manufacturersSliderWrapper.style.setProperty('--manufacturers-slider-before-width', `${sliderWrapperLenght}px`);
        manufacturersSliderWrapper.style.setProperty('--manufacturers-slider-before-left', `${sliderBeforeLeft}px`);
        return false;
    }

    !!manufacturersSlider.slidesEl && setSliderBeforeParams();
    !!manufacturersSlider.slidesEl && manufacturersSlider.on('resize', () => {
        (window.innerWidth > 768) && setSliderBeforeParams();
    });

    const prevProductsSlider = new Swiper('.prev-products-slider', {
        modules: [Navigation, Keyboard],
        slidesPerView: 1.25,
        spaceBetween: 16,
        keyboard: {
            enabled: true,
            onlyInViewport: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2.3,
                spaceBetween: 16
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 24
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 24
            },
            1280: {
                slidesPerView: 4,
                spaceBetween: 24
            },
            1520: {
                slidesPerView: 4,
                spaceBetween: 40
            },
        }
    });

    const otherNewsSlider = new Swiper('.other-news-slider', {
        modules: [Navigation, Keyboard],
        slidesPerView: 1.1,
        spaceBetween: 16,
        keyboard: {
            enabled: true,
            onlyInViewport: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2.3,
                spaceBetween: 16
            },
            768: {
                slidesPerView: 2.4,
                spaceBetween: 24
            },
            1280: {
                slidesPerView: 3,
                spaceBetween: 24
            },
            1520: {
                slidesPerView: 3,
                spaceBetween: 40
            },
        }
    });
    // END Sliders

    // Select
        const element = document.querySelectorAll('.js-select');
        element.forEach(select => {
            const choices = new Choices(select, {
                searchEnabled: false,
                shouldSort: false,
            });
        });
    // END Select

    // Input file
    const inputFiles = document.querySelectorAll('.js-input-file');
    !!inputFiles && inputFiles.forEach(inputFile => {
        inputFile.addEventListener('change', () => {
            const input = inputFile.querySelector('input');
            const label = inputFile.querySelector('span');
            label.textContent = input.files.length ? input.files[0].name : 'Загрузить файл';
        });
    });
    // END Input file

    // Smooth scroll to section
    const anchorLinks = document.querySelectorAll('.js-anchor-link');

    const scrollToSection = (e) => {
        const targetID = e.currentTarget.getAttribute('href').slice(1);

        document.getElementById(targetID).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    anchorLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            scrollToSection(e);
        })
    })
    // END Smooth scroll to section

    // Catalog filters
    const catalog = document.querySelector('.js-catalog');
    const catalogFilterBtn = document.querySelector('.js-catalog-filters-btn');
    const catalogApplyBtn = document.querySelector('.js-catalog-apply-btn');
    const closeFilterBtn = document.querySelector('.js-close-filters-btn');
    

    !!catalogFilterBtn && catalogFilterBtn.addEventListener('click', () => {
        catalog.classList.toggle('is-open-filters');
        isMenuOpen = !isMenuOpen;
        hiddenScrollMobile(isMenuOpen);
    });

    !!catalogApplyBtn && catalogApplyBtn.addEventListener('click', () => {
        catalog.classList.remove('is-open-filters');
        isMenuOpen = false;
        hiddenScrollMobile(isMenuOpen);
    });

    !!closeFilterBtn && closeFilterBtn.addEventListener('click', () => {
        catalog.classList.remove('is-open-filters');
        isMenuOpen = false;
        hiddenScrollMobile(isMenuOpen);
    });
    // END Catalog filters

    // More btn
    const characterictics = document.querySelectorAll('.js-characteristics');
    const moreCharactericticsBtns = document.querySelectorAll('.js-more-characterictics-btn');

    function checkCharactericticsHeight() {
        !!characterictics && characterictics.forEach((list, i) => {
            const listWrap = list.querySelector('.js-characteristics-wrap');
            const listMoreBtn = list.querySelector('.js-more-characterictics-btn');
            const listMoreBtnText = list.querySelector('.js-more-characterictics-btn span');

            if ((listWrap.scrollHeight > 200) && !list.classList.contains('is-show-more-btn')) {
                list.classList.add('is-show-more-btn');
                listWrap.style.maxHeight = '20rem';
                listMoreBtn.setAttribute('aria-expanded', false);
                listMoreBtnText.textContent = 'Показать больше';
            } else if ((listWrap.scrollHeight <= 200) && list.classList.contains('is-show-more-btn')) {
                list.classList.remove('is-show-more-btn');
                listWrap.style.maxHeight = 'none';
                listMoreBtnText.textContent = 'Скрыть';
            }
        });
    }

    if (window.innerWidth <= 480) {
        checkCharactericticsHeight();
    }


    !!moreCharactericticsBtns && moreCharactericticsBtns.forEach(btn => {
        !!btn && btn.addEventListener('click', (e) => {
            const content = e.currentTarget.previousElementSibling;
            const expanded = e.currentTarget.getAttribute('aria-expanded') === 'true';

            if (!expanded) {
                e.currentTarget.setAttribute('aria-expanded', !expanded);
                content.style.maxHeight = content.scrollHeight + 'px';
                e.currentTarget.querySelector('span').textContent = 'Скрыть';
            } else {
                e.currentTarget.setAttribute('aria-expanded', !expanded);
                content.style.maxHeight = '20rem';
                e.currentTarget.querySelector('span').textContent = 'Показать больше';
            }
        });
    });
    // END More btn

    // Tabs
    const tabs = document.querySelectorAll('.js-tab');
    
    !!tabs && tabs.forEach(tab => {
        if (tab.checked) {
            const tabContent = document.querySelector(`[data-tab="${tab.id}"]`);
            tabContent.classList.add('is-open');
        }
    })

    !!tabs && tabs.forEach(tab => {
        tab.addEventListener('change', (e) => {
            const currentChoice = e.target.id;
            const currentChoiceBlock = document.querySelector(`[data-tab="${currentChoice}"]`);

            if (e.target.checked) {
                const nameTabs = document.querySelectorAll(`[name="${e.target.name}"]`);
                nameTabs.forEach(tab => {
                    const tabContent = document.querySelector(`[data-tab="${tab.id}"]`);
                    if (tabContent.classList.contains('is-open')) {
                        tabContent.classList.remove('is-open');
                    }
                })
                currentChoiceBlock.classList.add('is-open');
            }
        })
    })
    // END Tabs

    // Display tooltip on account page

    const accountProductTooltips = document.querySelectorAll('.js-account-tooltip');
    const accountTabWithTooltips = document.querySelectorAll('.js-tab-with-tooltips');

    function setDisplayAccountTooltip() {
        !!accountProductTooltips && accountProductTooltips.forEach(tooltip => {
            const tooltipTitle = tooltip.querySelector('.js-account-tooltip-title');

            if ((tooltipTitle.clientHeight === tooltipTitle.scrollHeight) && (tooltip.classList.contains('is-display-tooltip'))) {
                tooltip.classList.remove('is-display-tooltip');
            } else if ((tooltipTitle.clientHeight < (tooltipTitle.scrollHeight - 1)) && (!tooltip.classList.contains('is-display-tooltip'))) {
                tooltip.classList.add('is-display-tooltip');
            }
        });
    }

    !!accountTabWithTooltips && accountTabWithTooltips.forEach(tab => {
        tab.addEventListener('click', () => {
            setTimeout(() => {
                setDisplayAccountTooltip();
            }, 500);
        });
    });

    // END Display tooltip on account page

    //Click outside
    document.addEventListener('mousedown', (e) => {
        const target = e.target;
        if (!headerSubmenu.contains(target) && !headerMenuBtn.contains(target) && headerSubmenu.classList.contains('is-open')) {
            closeSubmenu();
        }

        if (!headerMenu.contains(target) && !headerMenuBtn.contains(target) && header.classList.contains('is-open-menu')) {
            closeMenu();
        }

        if (!headerCatalogMenu.contains(target) && !headerCatalogBtn.contains(target) && headerCatalog.classList.contains('is-open')) {
            closeCatalog();
        }

        // Закрываем модальное окно при клике вне его области
        // Но не закрываем, если клик был на кнопке открытия или если модальное окно только что открыто
        const clickedModal = target.closest('.js-modal');
        const clickedModalTrigger = target.closest('[data-modal-trigger]');
        
        if (!target.closest('.js-modal-dialog') && clickedModal && !clickedModalTrigger) {
            // Проверяем, не было ли модальное окно только что открыто (предотвращаем немедленное закрытие)
            if (!clickedModal.dataset.justOpened || clickedModal.dataset.justOpened === 'false') {
                closeModal(clickedModal);
            }
        }
    });
    //END Click outside    
})

function fix100vh() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
}

function hiddenScrollMobile(isHidden) {
    if (window.innerWidth <= 768 && isHidden && !document.body.classList.contains('no-scroll')) {
        document.body.classList.add('no-scroll');
    } else if (!isHidden && document.body.classList.contains('no-scroll')) {
        document.body.classList.remove('no-scroll');
    }
}