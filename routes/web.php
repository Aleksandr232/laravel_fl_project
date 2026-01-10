<?php

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


//Главная страница
Route::get('', [FrontendController::class, 'index'])->name('frontend.index');
//Страницы и разделы
Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('frontend.page');
//Пригласить на тендер
Route::get('/invite', [FrontendController::class, 'invite'])->name('frontend.invite');
Route::post('/invite', [FrontendController::class, 'sendInvite'])->name('frontend.send_invite');
//Запрос номенклатуры
Route::get('/nomenclature-request', [FrontendController::class, 'nomenclatureRequest'])->name('frontend.nomenclature_request');
Route::post('/send-nomenclature-request', [FrontendController::class, 'sendNomenclatureRequest'])->name('frontend.send_nomenclature_request');
//Новости
Route::get('/news', [FrontendController::class, 'news'])->name('frontend.news');
//Описание новости
Route::get('/news/{slug}', [FrontendController::class, 'newsItem'])->name('frontend.news_item');
//каталог
Route::get('/catalog/{slug?}', [FrontendController::class, 'catalog'])->name('frontend.catalog');
//Контакты
Route::get('/contacts', [FrontendController::class, 'contacts'])->name('frontend.contacts');
Route::post('/contacts', [FrontendController::class, 'sendFeedback'])->name('frontend.send_feedback');
//Конверторы
Route::get('/converters', [FrontendController::class, 'converters'])->name('frontend.converters');
//Производители
Route::get('/manufacturers', [FrontendController::class, 'manufacturers'])->name('frontend.manufacturers');
// Описание производителя
Route::get('/manufacturer/{slug}', [FrontendController::class, 'manufacturer'])->name('frontend.manufacturer');

Route::any('/get-subcategory', [FrontendController::class, 'getSubcategory'])->name('frontend.get_subcategory');

// Доставка и оплата
Route::any('/conditions', [FrontendController::class, 'conditions'])->name('frontend.conditions');

Route::get('product/{slug}', [FrontendController::class, 'product'])->name('frontend.product');


Route::group(['prefix' => 'profile', 'middleware' => ['auth:client']], function () {
    Route::get('', [ProfileController::class, 'index'])->name('frontend.profile.index');
    Route::post('/update', [ProfileController::class, 'update'])->name('frontend.profile.update');
    Route::post('/update-company', [ProfileController::class, 'updateCompany'])->name('frontend.profile.update-company');
});

// Ajax маршруты для авторизации
Route::post('/ajax/login', [AuthController::class, 'login'])->name('frontend.ajax.login');
Route::post('/ajax/register', [AuthController::class, 'register'])->name('frontend.ajax.register');
Route::post('/ajax/logout', [AuthController::class, 'logout'])->name('frontend.ajax.logout');
Route::post('/ajax/recover-password', [AuthController::class, 'recoverPassword'])->name('frontend.ajax.recover-password');