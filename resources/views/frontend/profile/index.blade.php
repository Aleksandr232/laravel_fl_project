@extends('layouts.frontend')

@section('title', $title)

@section('description', $meta_description)

@section('keywords', $meta_keywords)

@section('seo_url_canonical', $seo_url_canonical)

@section('content')

    @include('layouts._breadcrumbs')


    <div class="account container-md">
        <ul class="account__tabs tabs">
            <li class="tab">
                <input type="radio" name="account" id="account-profile" class="js-tab" checked="">
                <label for="account-profile">
                    <svg aria-hidden="true">
                        <use xlink:href="{{ url('/images/sprite.svg#profile') }}"></use>
                    </svg>
                    Профиль
                </label>
            </li>
            <li class="tab">
                <input type="radio" name="account" id="account-orders" class="js-tab">
                <label for="account-orders">
                    <svg aria-hidden="true">
                        <use xlink:href="{{ url('/images/sprite.svg#orders') }}"></use>
                    </svg>
                    Мои заказы
                </label>
            </li>
            <li class="tab">
                <input type="radio" name="account" id="account-requests" class="js-tab">
                <label for="account-requests">
                    <svg aria-hidden="true">
                        <use xlink:href="{{ url('/images/sprite.svg#request') }}s"></use>
                    </svg>
                    Мои запросы
                </label>
            </li>
            <li class="tab">
                <input type="radio" name="account" id="account-claims" class="js-tab js-tab-with-tooltips">
                <label for="account-claims">
                    <svg aria-hidden="true">
                        <use xlink:href="{{ url('/images/sprite.svg#claims') }}"></use>
                    </svg>
                    Претензии
                </label>
            </li>
            <li class="tab">
                <button type="button" class="tab__btn btn" id="logout-btn">
                    <svg aria-hidden="true">
                        <use xlink:href="{{ url('/images/sprite.svg#logout') }}"></use>
                    </svg>
                    <span>Выйти</span>
                </button>
            </li>
        </ul>
        <div class="account__tabs-content tabs-content">
            <div data-tab="account-profile" class="profile">
                <section>
                    <div class="account__section-header">
                        <div class="account__section-title">
                            <h2>Общая информация</h2>
                        </div>
                        <button type="button" class="profile__edit-btn btn btn--icon" data-modal-trigger="general-info">
                            <span class="sr-only">Редактировать общую информацию</span>
                            <svg aria-hidden="true">
                                <use xlink:href="{{ url('/images/sprite.svg#edit') }}"></use>
                            </svg>
                        </button>
                    </div>
                    <div class="profile__info">
                        <dl class="profile__list general">
                            @if($client->phone)
                            <div class="profile__point">
                                <dt class="profile__point-title">Номер телефона</dt>
                                <dd class="profile__point-text">{{ $client->phone }}</dd>
                            </div>
                            @endif
                            <div class="profile__point">
                                <dt class="profile__point-title">Email</dt>
                                <dd class="profile__point-text">{{ $client->email }}</dd>
                            </div>
                            <div class="profile__point">
                                <dt class="profile__point-title">Пароль</dt>
                                <dd class="profile__point-text">***********</dd>
                            </div>
                            <div class="profile__point">
                                <dt class="profile__point-title">Имя</dt>
                                <dd class="profile__point-text">{{ $client->name }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>
                <section>
                    <div class="account__section-header">
                        <div class="account__section-title">
                            <h2>Информация о&nbsp;компании</h2>
                        </div>
                        <button type="button" class="profile__edit-btn btn btn--icon" data-modal-trigger="company-info">
                            <span class="sr-only">Редактировать информацию о компании</span>
                            <svg aria-hidden="true">
                                <use xlink:href="{{ url('/images/sprite.svg#edit') }}"></use>
                            </svg>
                        </button>
                    </div>
                    <div class="profile__info">
                        <dl class="profile__list">
                            <div class="profile__point">
                                <dt class="profile__point-title">Компания</dt>
                                <dd class="profile__point-text">{{ $company->name ?? 'ООО “ЭлектроМонтаж” ' }}</dd>
                            </div>
                            <div class="profile__point">
                                <dt class="profile__point-title">ИНН</dt>
                                <dd class="profile__point-text">{{ $company->inn ?? '—' }}</dd>
                            </div>
                        </dl>
                        <dl class="profile__list">
                            @if($company)
                            <div class="profile__point">
                                <dt class="profile__point-title">Контактное лицо</dt>
                                <dd class="profile__point-text">{{ $company->contact_person ?? '—' }}</dd>
                            </div>
                            @if($company->phone)
                            <div class="profile__point">
                                <dt class="profile__point-title">Номер телефона</dt>
                                <dd class="profile__point-text">{{ $company->phone }}</dd>
                            </div>
                            @endif
                            <div class="profile__point">
                                <dt class="profile__point-title">Электронная почта</dt>
                                <dd class="profile__point-text">{{ $company->email ?? '—' }}</dd>
                            </div>
                            @else
                            <div class="profile__point">
                                <dt class="profile__point-title">Контактное лицо</dt>
                                <dd class="profile__point-text">—</dd>
                            </div>
                            <div class="profile__point">
                                <dt class="profile__point-title">Номер телефона</dt>
                                <dd class="profile__point-text">—</dd>
                            </div>
                            <div class="profile__point">
                                <dt class="profile__point-title">Электронная почта</dt>
                                <dd class="profile__point-text">—</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </section>
            </div>
            <div data-tab="account-orders" class="orders">
                <section class="account__container">
                    <div class="account__section-title">
                        <h2>Мои заказы</h2>
                    </div>
                    <div class="account__table">
                        <div class="account__table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th>Номер заказа</th>
                                    <th>Дата создания</th>
                                    <th>Сумма заказа</th>
                                    <th>Ожидаемая<br>дата доставки</th>
                                    <th>Статус заказа</th>
                                    <th>Счет на оплату</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-nowrap"></td>
                                    <td></td>
                                    <td>
                                        <div class="account__table-status create">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#new-doc"></use>
                                            </svg>
                                            <span>Создан</span>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-nowrap"></td>
                                    <td></td>
                                    <td>
                                        <div class="account__table-status in-progress">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#cogwheel"></use>
                                            </svg>
                                            <span>В работе</span>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">24.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">15 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status issued">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#fact-check"></use>
                                            </svg>
                                            <span>Оформлен</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>122</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">22.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">20 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status paid">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#money"></use>
                                            </svg>
                                            <span>Оплачен</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>122</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">22.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">20 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status ready">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#box"></use>
                                            </svg>
                                            <span>Готов к&nbsp;выдаче</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>121</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">20.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">200 000 р.</td>
                                    <td class="text-right">24.09.2025</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Выдан</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">200 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Выдан</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">200 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Выдан</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">200 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Выдан</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="account__table-numb">
                                            <span>120</span>
                                            <button type="button" class="btn btn--secondary btn--sm btn--icon" data-modal-trigger="order-details">
                                                <span class="sr-only">Детали заказа</span>
                                                <svg aria-hidden="true">
                                                    <use xlink:href="images/sprite.svg#details"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-right">15.08.2025</td>
                                    <td class="text-right text-medium text-nowrap">200 000 р.</td>
                                    <td class="text-right">20.09.2025</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Выдан</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#download"></use>
                                            </svg>
                                            <span>Скачать счёт</span>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="account__more-btn btn btn--secondary">
                        <span>Показать еще</span>
                    </button>
                </section>
            </div>
            <div data-tab="account-requests" class="requests">
                <section class="account__container">
                    <div class="account__section-title">
                        <h2>Мои запросы</h2>
                    </div>
                    <div class="account__table">
                        <div class="account__table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th>Номер запроса</th>
                                    <th>Дата создания</th>
                                    <th>Запрашиваемая<br>номенклатура</th>
                                    <th>Запрашиваемое<br>количество</th>
                                    <th>Статус запроса</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>120</td>
                                    <td class="text-right">15.08.2025</td>
                                    <td></td>
                                    <td class="text-medium text-right"></td>
                                    <td>
                                        <div class="account__table-status create">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#new-doc"></use>
                                            </svg>
                                            <span>Создан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>120</td>
                                    <td class="text-right">15.08.2025</td>
                                    <td></td>
                                    <td class="text-medium text-right"></td>
                                    <td>
                                        <div class="account__table-status in-progress">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#cogwheel"></use>
                                            </svg>
                                            <span>В работе</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>121</td>
                                    <td class="text-right">20.08.2025</td>
                                    <td>34583687527368Asdf45</td>
                                    <td class="text-medium text-right text-nowrap">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Обработан</span>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="account__more-btn btn btn--secondary">
                        <span>Показать еще</span>
                    </button>
                </section>
            </div>
            <div data-tab="account-claims" class="claims">
                <section class="account__container">
                    <div class="account__section-header">
                        <div class="account__section-title">
                            <h2>Претензии</h2>
                        </div>
                        <button type="button" class="btn btn--tertiary btn--sm" data-modal-trigger="create-claim">
                            <svg aria-hidden="true">
                                <use xlink:href="images/sprite.svg#plus"></use>
                            </svg>
                            <span>Создать</span>
                        </button>
                    </div>
                    <div class="account__table">
                        <div class="account__table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th>Тип претензии</th>
                                    <th>По счету</th>
                                    <th>Позиция</th>
                                    <th>Количество<br>в счете</th>
                                    <th>Количество<br>с браком</th>
                                    <th>Статус претензии</th>
                                    <th>Результат<br>рассмотрения</th>
                                    <th>Бланк</th>
                                    <th>Фото</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52] ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52] ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status create">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#cogwheel"></use>
                                            </svg>
                                            <span>В обработке</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result"></td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Недопоставка</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status in-progress">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#time"></use>
                                            </svg>
                                            <span>В обработке</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result"></td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status agreement">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#folder"></use>
                                            </svg>
                                            <span>Согласование</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result"></td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Возврат</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status expertise">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#search"></use>
                                            </svg>
                                            <span>Отправлено на экспертизу</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result"></td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status denied">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#cancel"></use>
                                            </svg>
                                            <span>Отказано</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result"></td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status done">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#check-circle"></use>
                                            </svg>
                                            <span>Одобрено</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result">Замена</td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status return">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#back"></use>
                                            </svg>
                                            <span>К возврату</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result">Замена</td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status close">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#close-circle"></use>
                                            </svg>
                                            <span>Закрыто</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result">Замена</td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status checked">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#fact-check"></use>
                                            </svg>
                                            <span>Проверено складом</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result">Замена</td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Брак</td>
                                    <td>№123 от 28.09.2025</td>
                                    <td>
                                        <div class="account__table-product tooltip js-account-tooltip">
                                            <div class="tooltip__body js-account-tooltip-tbody" role="tooltip">
                                                <span>ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                            </div>
                                            <span class="account__table-title js-account-tooltip-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</span>
                                        </div>
                                    </td>
                                    <td class="text-medium text-nowrap text-right">10 000</td>
                                    <td class="account__table-defect-count">10 000</td>
                                    <td>
                                        <div class="account__table-status checked">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#fact-check"></use>
                                            </svg>
                                            <span>Проверено складом</span>
                                        </div>
                                    </td>
                                    <td class="account__table-result">Замена</td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать бланк">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#requests"></use>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="./images/logo.svg" download="logo.svg" class="btn btn--secondary btn--sm btn--icon" aria-label="Скачать фото">
                                            <svg aria-hidden="true">
                                                <use xlink:href="images/sprite.svg#image"></use>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="account__more-btn btn btn--secondary">
                        <span>Показать еще</span>
                    </button>
                </section>
            </div>
        </div>
    </div>

    <div class="modal js-modal" data-modal-name="general-info">
        <div class="modal__wrap">
            <div class="modal__dialog js-modal-dialog" role="dialog" aria-modal="true">
                <button type="button" class="modal__close-btn btn btn--icon btn--sm js-modal-close">
                    <span class="sr-only">Закрыть модальное окно</span>
                    <svg aria-hidden="true">
                        <use xlink:href="images/sprite.svg#close"></use>
                    </svg>
                </button>
                <div class="modal__content">
                    <div class="modal__title">
                        <h2>Изменить общую информацию</h2>
                    </div>
                    <form class="modal__form" id="general-info-form">
                        <div class="result"></div>
                        <div class="form-input">
                            <label for="general-info-phone">Номер телефона*</label>
                            <input type="tel" id="general-info-phone" name="phone" placeholder='+7 900 000-00-00' value="{{ $client->phone ?? '' }}">
                        </div>
                        <div class="form-input">
                            <label for="general-info-email">Email*</label>
                            <input type="email" id="general-info-email" name="email" placeholder='user@gmail.com' value="{{ $client->email ?? '' }}" readonly>
                        </div>
                        <div class="form-password">
                            <label for="general-info-password">Новый пароль (оставьте пустым, если не хотите менять)</label>
                            <input type="password" id="general-info-password" name="password" placeholder='*******'>
                            <div class="form-display-btn">
                                <input type="checkbox" id="general-info-display-password">
                                <label for="general-info-display-password">Показать пароль</label>
                            </div>
                        </div>
                        <div class="form-password">
                            <label for="general-info-repeat-password">Повторите пароль</label>
                            <input type="password" id="general-info-repeat-password" name="password_confirmation" placeholder='*******'>
                            <div class="form-display-btn">
                                <input type="checkbox" id="general-info-display-repeat-password">
                                <label for="general-info-display-repeat-password">Показать пароль</label>
                            </div>
                        </div>
                        <div class="form-input">
                            <label for="general-info-name">Ваше имя</label>
                            <input type="text" id="general-info-name" name="name" placeholder='Иван Иванов' value="{{ $client->name ?? '' }}">
                        </div>
                        <div class="modal__btns">
                            <button type="submit" class="btn btn--primary">
                                <svg aria-hidden="true">
                                    <use xlink:href="{{ url('images/sprite.svg#save') }}"></use>
                                </svg>
                                <span>Сохранить</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal js-modal" data-modal-name="company-info">
        <div class="modal__wrap">
            <div class="modal__dialog js-modal-dialog" role="dialog" aria-modal="true">
                <button type="button" class="modal__close-btn btn btn--icon btn--sm js-modal-close">
                    <span class="sr-only">Закрыть модальное окно</span>
                    <svg aria-hidden="true">
                        <use xlink:href="images/sprite.svg#close"></use>
                    </svg>
                </button>
                <div class="modal__content">
                    <div class="modal__title">
                        <h2>Изменить информацию о&nbsp;компании</h2>
                    </div>
                    <form class="modal__form" id="company-info-form">
                        <div class="result"></div>
                        <div class="form-input">
                            <label for="company-info-company-name">Название компании*</label>
                            <input type="text" id="company-info-company-name" name="name" placeholder='ООО “ЭлектроМонтаж”' required>
                        </div>
                        <div class="form-input">
                            <label for="company-info-inn">ИНН*</label>
                            <input type="text" id="company-info-inn" name="inn" placeholder='1122312321428234' value="{{ $company->inn ?? '' }}" required>
                        </div>
                        <div class="form-input">
                            <label for="company-info-name">Контактное лицо*</label>
                            <input type="text" id="company-info-name" name="contact_person" placeholder='Петр Петров' value="{{ $company->contact_person ?? '' }}" required>
                        </div>
                        <div class="form-input">
                            <label for="company-info-phone">Номер телефона</label>
                            <input type="tel" id="company-info-phone" name="phone" placeholder='+7 900 000-00-00' value="{{ $company->phone ?? '' }}">
                        </div>
                        <div class="form-input">
                            <label for="company-info-email">Электронная почта*</label>
                            <input type="email" id="company-info-email" name="email" placeholder='customer@gmail.com' value="{{ $company->email ?? '' }}" required>
                        </div>
                        <div class="modal__btns">
                            <button type="submit" class="btn btn--primary">
                                <svg aria-hidden="true">
                                    <use xlink:href="images/sprite.svg#save"></use>
                                </svg>
                                <span>Сохранить</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal--order-details js-modal" data-modal-name="order-details">
        <div class="modal__wrap">
            <div class="modal__dialog js-modal-dialog" role="dialog" aria-modal="true">
                <button type="button" class="modal__close-btn btn btn--icon btn--sm js-modal-close">
                    <span class="sr-only">Закрыть модальное окно</span>
                    <svg aria-hidden="true">
                        <use xlink:href="images/sprite.svg#close"></use>
                    </svg>
                </button>
                <div class="modal__content">
                    <div class="modal__title">
                        <h2>Детали заказа</h2>
                    </div>
                    <div class="modal__order-list">
                        <ul class="order-list">
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-1.webp" type="image/webp">
                                        <img src="./images/products/product-1.jpg" alt="ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-2.webp" type="image/webp">
                                        <img src="./images/products/product-2.jpg" alt="FSCQ0765RTYDTU, Импульсный регулятор напряжения [TO-220-5 FP (Formed Leads)]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">FSCQ0765RTYDTU, Импульсный регулятор напряжения [TO-220-5 FP (Formed Leads)]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-3.webp" type="image/webp">
                                        <img src="./images/products/product-3.jpg" alt="FSFR1800XSL, Контроллер резонансного ИИП со встроенным ключом 120Вт [SIP-9 L-Forming]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">FSFR1800XSL, Контроллер резонансного</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-4.webp" type="image/webp">
                                        <img src="./images/products/product-4.jpg" alt="ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-1.webp" type="image/webp">
                                        <img src="./images/products/product-1.jpg" alt="ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-2.webp" type="image/webp">
                                        <img src="./images/products/product-2.jpg" alt="FSCQ0765RTYDTU, Импульсный регулятор напряжения [TO-220-5 FP (Formed Leads)]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">FSCQ0765RTYDTU, Импульсный регулятор напряжения [TO-220-5 FP (Formed Leads)]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-3.webp" type="image/webp">
                                        <img src="./images/products/product-3.jpg" alt="FSFR1800XSL, Контроллер резонансного ИИП со встроенным ключом 120Вт [SIP-9 L-Forming]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">FSFR1800XSL, Контроллер резонансного</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                            <li class="order-list__item">
                                <div class="order-list__item-img">
                                    <picture>
                                        <source srcset="./images/products/product-4.webp" type="image/webp">
                                        <img src="./images/products/product-4.jpg" alt="ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]">
                                    </picture>
                                </div>
                                <div class="order-list__item-info">
                                    <a href="./product-details.html" class="order-list__item-title">ADUC812BSZ, Микроконвертер, 12-Bit ADC, 8-bit ADuC8xx 8052 CISC 8KB Flash 3.3V/5V [MQFP-52]</a>
                                    <span class="order-list__item-count">Количество: 10</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal--create-claim js-modal" data-modal-name="create-claim">
        <div class="modal__wrap">
            <div class="modal__dialog js-modal-dialog" role="dialog" aria-modal="true">
                <button type="button" class="modal__close-btn btn btn--icon btn--sm js-modal-close">
                    <span class="sr-only">Закрыть модальное окно</span>
                    <svg aria-hidden="true">
                        <use xlink:href="images/sprite.svg#close"></use>
                    </svg>
                </button>
                <div class="modal__content">
                    <div class="modal__title">
                        <h2>Создать претензию</h2>
                    </div>
                    <form class="modal__form is-success">
                        <div class="form-input__line">
                            <div class="form-select">
                                <label for="create-claim-type">Тип претензии</label>
                                <select name="platform" id="create-claim-type" class="js-select">
                                    <option value="claim-type-1">Брак</option>
                                    <option value="claim-type-2">Недопоставка</option>
                                    <option value="claim-type-3">Возврат</option>
                                </select>
                            </div>
                            <div class="form-select">
                                <label for="create-claim-invoice-numb">Номер счета</label>
                                <select name="platform" id="create-claim-invoice-numb" class="js-select">
                                    <option value="claim-invoice-1">№123 от 28.09.2025</option>
                                    <option value="claim-invoice-2">№12 от 28.08.2025</option>
                                    <option value="claim-invoice-3">№1 от 10.07.2025</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-select">
                            <label for="create-claim-product">Наименование позиции</label>
                            <select name="platform" id="create-claim-product" class="js-select">
                                <option value="claim-product-1">HMC542BLP4ETR, Широкополосный 5-бит цифровой аттенюатор c serial-parallel драйвером, DC-4ГГц [VFQFN-24 EP.]</option>
                                <option value="claim-product-2">HMC542BLP4ETR, Широкополосный 5-бит цифровой аттенюатор c serial-parallel драйвером, DC-4ГГц [VFQFN-24 EP.]</option>
                                <option value="claim-product-3">HMC542BLP4ETR, Широкополосный 5-бит цифровой аттенюатор c serial-parallel драйвером, DC-4ГГц [VFQFN-24 EP.]</option>
                                <option value="claim-product-4">HMC542BLP4ETR, Широкополосный 5-бит цифровой аттенюатор c serial-parallel драйвером, DC-4ГГц [VFQFN-24 EP.]</option>
                                <option value="claim-product-5">HMC542BLP4ETR, Широкополосный 5-бит цифровой аттенюатор c serial-parallel драйвером, DC-4ГГц [VFQFN-24 EP.]</option>
                            </select>
                        </div>
                        <div class="form-input__line">
                            <div class="form-input">
                                <label for="create-claim-count-invoice">Количество в счете</label>
                                <input type="number" id="create-claim-count-invoice" value="1000" readonly>
                            </div>
                            <div class="form-input">
                                <label for="create-claim-count-defect">Количество с браком</label>
                                <input type="number" id="create-claim-count-defect" placeholder="1">
                            </div>
                        </div>
                        <div class="form-input__btns-line">
                            <div class="form-input-file js-input-file">
                                <label for="create-claim-form" class="btn btn--secondary">
                                    <svg aria-hidden="true">
                                        <use xlink:href="images/sprite.svg#file-download"></use>
                                    </svg>
                                    <span>Загрузить претензию на официальном бланке</span>
                                </label>
                                <input type="file" id="create-claim-form" class="sr-only">
                            </div>
                            <div class="form-input-file js-input-file">
                                <label for="create-claim-image" class="btn btn--secondary">
                                    <svg aria-hidden="true">
                                        <use xlink:href="images/sprite.svg#image-add"></use>
                                    </svg>
                                    <span>Загрузить фото</span>
                                </label>
                                <input type="file" id="create-claim-image" class="sr-only">
                            </div>
                        </div>
                        <div class="modal__btns">
                            <button type="button" class="btn btn--primary">
                                <svg aria-hidden="true">
                                    <use xlink:href="images/sprite.svg#save"></use>
                                </svg>
                                <span>Сохранить</span>
                            </button>
                        </div>
                        <span class="success-message">
                            <svg aria-hidden="true">
                                <use xlink:href="images/sprite.svg#check-circle"></use>
                            </svg>
                            <span>Претензия успешно создана</span>
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
