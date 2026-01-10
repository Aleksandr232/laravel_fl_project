<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Profile\UpdateProfileRequest;
use App\Helpers\MenuHelper;
use App\Mail\PasswordChangedMail;
use App\Models\Catalog;
use App\Models\Feedback;
use App\Models\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Encryption\MissingAppKeyException;

class ProfileController extends Controller
{
    /**
     * Страница профиля
     *
     * @return View
     */
    public function index(): View
    {
        $seo = Seo::getSeo('frontend.profile', 'Личный кабинет');
        $title = $seo['title'];
        $meta_description = $seo['meta_description'];
        $meta_keywords = $seo['meta_keywords'];
        $meta_title = $seo['meta_title'];
        $seo_url_canonical = $seo['seo_url_canonical'];
        $h1 = $seo['h1'];

        $menu = MenuHelper::getMenuList();
        $catalogsList = Catalog::getCatalogList();
        $catalogs = Catalog::orderBy('name')->where('parent_id', 0)->get();
        $options = Feedback::getPlatformList();
        $client = Auth::guard('client')->user();

        return view('frontend.profile.index', compact(
                'meta_description',
                'meta_keywords',
                'meta_title',
                'menu',
                'options',
                'catalogs',
                'catalogsList',
                'h1',
                'seo_url_canonical',
                'title',
                'client'
            )
        )->with('title', $title);
    }

    public function orders()
    {

    }

    public function complaints()
    {

    }

    /**
     * Обновление профиля пользователя
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $client = Auth::guard('client')->user();
            $changedFields = [];
            $oldEmail = $client->email;

            // Обновляем имя, если оно передано
            if ($request->filled('name')) {
                $client->name = $request->name;
                $changedFields[] = 'name';
            }

            // Обновляем пароль, если он передан и не пустой
            if ($request->filled('password') && $request->password !== null && trim($request->password) !== '') {
                $client->password = Hash::make($request->password);
                $changedFields[] = 'password';
            }

            $client->save();

            // Отправляем письмо, если были изменены пароль или email
            if (!empty($changedFields) && (in_array('password', $changedFields) || in_array('email', $changedFields))) {
                try {
                    // Проверяем, что APP_KEY установлен перед отправкой письма
                    if (config('app.key')) {
                        Mail::to($client->email)->send(new PasswordChangedMail($client, $changedFields));
                    } else {
                        \Log::warning('APP_KEY не установлен. Письмо не отправлено.');
                    }
                } catch (\Illuminate\Encryption\MissingAppKeyException $e) {
                    // Логируем ошибку, но не прерываем процесс обновления
                    \Log::error('Ошибка: APP_KEY не установлен. Письмо не отправлено.');
                } catch (\Exception $e) {
                    // Логируем ошибку отправки письма, но не прерываем процесс обновления
                    \Log::error('Ошибка отправки письма об изменении пароля: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Данные успешно обновлены',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении данных: ' . $e->getMessage(),
            ], 422);
        }
    }
}
