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
use Symfony\Component\Mailer\Exception\TransportException;

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
            $oldPhone = $client->phone;
            $oldName = $client->name;

            Log::info('Начало обновления профиля', [
                'user_id' => $client->id,
                'request_data' => $request->all()
            ]);

            // Обновляем имя, если оно передано и отличается
            if ($request->has('name')) {
                $newName = trim($request->input('name', ''));
                $currentName = trim($client->name ?? '');
                if ($newName !== $currentName) {
                    $client->name = $newName;
                    $changedFields[] = 'name';
                    Log::info('Имя изменено', ['old' => $oldName, 'new' => $newName]);
                }
            }

            // Обновляем телефон, если он передан и отличается
            if ($request->has('phone')) {
                $newPhone = trim($request->input('phone', ''));
                $currentPhone = trim($client->phone ?? '');
                if ($newPhone !== $currentPhone) {
                    $client->phone = $newPhone;
                    $changedFields[] = 'phone';
                    Log::info('Телефон изменен', ['old' => $oldPhone, 'new' => $newPhone]);
                }
            }

            // Обновляем пароль, если он передан и не пустой
            if ($request->filled('password') && $request->password !== null && trim($request->password) !== '') {
                $client->password = Hash::make($request->password);
                $changedFields[] = 'password';
                Log::info('Пароль изменен');
            }

            Log::info('Измененные поля', ['changedFields' => $changedFields]);

            // Сохраняем изменения только если что-то изменилось
            if (!empty($changedFields)) {
                $client->save();
                Log::info('Данные профиля сохранены');
            } else {
                Log::info('Нет изменений для сохранения');
            }

            // Отправляем письмо, если были изменены пароль, email или другие важные данные
            if (!empty($changedFields)) {
                // Отправляем письмо при изменении пароля, телефона или других критичных данных
                $shouldSendEmail = in_array('password', $changedFields) || 
                                  in_array('email', $changedFields) || 
                                  in_array('phone', $changedFields);
                
                Log::info('Проверка отправки письма', [
                    'shouldSendEmail' => $shouldSendEmail,
                    'changedFields' => $changedFields,
                    'user_email' => $client->email
                ]);
                
                if ($shouldSendEmail) {
                    try {
                        // Проверяем, что APP_KEY установлен перед отправкой письма
                        $appKey = config('app.key');
                        Log::info('Проверка APP_KEY', ['has_key' => !empty($appKey)]);
                        
                        if ($appKey) {
                            // Проверяем настройки почты
                            $mailDriver = config('mail.default');
                            Log::info('Настройки почты', [
                                'driver' => $mailDriver,
                                'from_address' => config('mail.from.address'),
                                'from_name' => config('mail.from.name')
                            ]);
                            
                            Log::info('Начало отправки письма', [
                                'to' => $client->email,
                                'changedFields' => $changedFields
                            ]);
                            
                            // Отправляем письмо синхронно (не в очередь)
                            Mail::to($client->email)->send(new PasswordChangedMail($client, $changedFields));
                            
                            Log::info('Письмо об изменении данных успешно отправлено пользователю: ' . $client->email, [
                                'changedFields' => $changedFields,
                                'mail_driver' => $mailDriver
                            ]);
                        } else {
                            Log::warning('APP_KEY не установлен. Письмо не отправлено пользователю: ' . $client->email);
                        }
                    } catch (\Illuminate\Encryption\MissingAppKeyException $e) {
                        // Логируем ошибку, но не прерываем процесс обновления
                        Log::error('Ошибка: APP_KEY не установлен. Письмо не отправлено пользователю: ' . $client->email);
                    } catch (TransportException | \Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                        // Ошибки SMTP (неправильные учетные данные, проблемы с сервером и т.д.)
                        $errorMessage = $e->getMessage();
                        Log::error('Ошибка SMTP при отправке письма для ' . $client->email . ': ' . $errorMessage);
                        
                        // Проверяем тип ошибки для более детального логирования
                        if (str_contains($errorMessage, 'Invalid login or password') || 
                            str_contains($errorMessage, '535') || 
                            str_contains($errorMessage, 'authentication')) {
                            Log::error('Проблема с учетными данными SMTP. Проверьте MAIL_USERNAME и MAIL_PASSWORD в .env');
                            Log::error('Текущий MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
                            Log::error('Проверьте, что пароль правильный и нет лишних пробелов');
                        } elseif (str_contains($errorMessage, 'Connection') || str_contains($errorMessage, 'timeout')) {
                            Log::error('Проблема с подключением к SMTP серверу. Проверьте MAIL_HOST и MAIL_PORT');
                            Log::error('Текущие настройки: HOST=' . config('mail.mailers.smtp.host') . ', PORT=' . config('mail.mailers.smtp.port'));
                        }
                    } catch (\Exception $e) {
                        // Логируем ошибку отправки письма, но не прерываем процесс обновления
                        Log::error('Ошибка отправки письма об изменении пароля для ' . $client->email . ': ' . $e->getMessage());
                        Log::error('Класс исключения: ' . get_class($e));
                        
                        // Не логируем полный stack trace в production для безопасности
                        if (config('app.debug')) {
                            Log::error('Stack trace: ' . $e->getTraceAsString());
                        }
                    }
                }
            }

            $message = 'Данные успешно обновлены';
            $emailSent = false;
            
            if (!empty($changedFields)) {
                $shouldSendEmail = in_array('password', $changedFields) || 
                                  in_array('email', $changedFields) || 
                                  in_array('phone', $changedFields);
                if ($shouldSendEmail && config('app.key')) {
                    $message .= '. Уведомление отправлено на ваш email.';
                    $emailSent = true;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'changed_fields' => $changedFields,
                'email_sent' => $emailSent
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка в методе update профиля: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении данных: ' . $e->getMessage(),
            ], 422);
        }
    }
}
