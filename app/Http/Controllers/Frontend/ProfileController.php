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
                            
                            // Предупреждение, если драйвер - log
                            if ($mailDriver === 'log') {
                                Log::warning('⚠️ ВНИМАНИЕ: Драйвер почты установлен как "log". Письма НЕ будут отправляться реально, а только записываться в логи!');
                                Log::warning('Для реальной отправки писем измените MAIL_MAILER в .env файле на "smtp" и настройте параметры SMTP (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD).');
                                Log::warning('После изменения выполните: php artisan config:clear');
                            }
                            
                            Log::info('Начало отправки письма', [
                                'to' => $client->email,
                                'changedFields' => $changedFields
                            ]);
                            
                            // Отправляем письмо синхронно (не в очередь)
                            Mail::to($client->email)->send(new PasswordChangedMail($client, $changedFields));
                            
                            if ($mailDriver === 'log') {
                                Log::info('Письмо записано в логи (драйвер: log). Для реальной отправки измените MAIL_MAILER на smtp', [
                                    'changedFields' => $changedFields,
                                    'user_email' => $client->email
                                ]);
                            } else {
                                Log::info('Письмо об изменении данных успешно отправлено пользователю: ' . $client->email, [
                                    'changedFields' => $changedFields,
                                    'mail_driver' => $mailDriver
                                ]);
                            }
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
                            $mailHost = config('mail.mailers.smtp.host');
                            $mailPort = config('mail.mailers.smtp.port');
                            $mailEncryption = config('mail.mailers.smtp.encryption');
                            $mailUsername = config('mail.mailers.smtp.username');
                            $mailPassword = config('mail.mailers.smtp.password');
                            
                            Log::error('❌ ПРОБЛЕМА С УЧЕТНЫМИ ДАННЫМИ SMTP');
                            Log::error('Текущие настройки в .env файле:');
                            Log::error('  - MAIL_USERNAME: ' . ($mailUsername ?: 'НЕ УСТАНОВЛЕН'));
                            Log::error('  - MAIL_PASSWORD: ' . ($mailPassword ? 'УСТАНОВЛЕН (длина: ' . strlen($mailPassword) . ' символов)' : 'НЕ УСТАНОВЛЕН'));
                            Log::error('  - MAIL_HOST: ' . ($mailHost ?: 'НЕ УСТАНОВЛЕН'));
                            Log::error('  - MAIL_PORT: ' . ($mailPort ?: 'НЕ УСТАНОВЛЕН'));
                            Log::error('  - MAIL_ENCRYPTION: ' . ($mailEncryption ?: 'НЕ УСТАНОВЛЕН'));
                            
                            // Проверка на специальные символы в пароле
                            if ($mailPassword && preg_match('/[!@#$%^&*()_+\-=\[\]{};\'\\:"|,.<>\/?]/', $mailPassword)) {
                                Log::error('');
                                Log::error('⚠️ ОБНАРУЖЕНЫ СПЕЦИАЛЬНЫЕ СИМВОЛЫ В ПАРОЛЕ!');
                                Log::error('Пароль содержит специальные символы, которые могут вызывать проблемы.');
                                Log::error('Рекомендуется: изменить пароль в панели SpaceWeb на пароль БЕЗ специальных символов');
                            }
                            
                            Log::error('');
                            Log::error('⚠️ ВАЖНО: Для SpaceWeb правильные настройки:');
                            Log::error('  - SSL: порт 465 с encryption=ssl');
                            Log::error('  - TLS: порт 2525 с encryption=tls');
                            Log::error('  - Без шифрования: порт 25 с encryption=null');
                            Log::error('');
                            Log::error('РЕШЕНИЕ (попробуйте в таком порядке):');
                            Log::error('');
                            Log::error('ШАГ 1: Проверьте логин и пароль');
                            Log::error('  - Попробуйте войти через https://webmail.sweb.ru');
                            Log::error('  - Если вход не работает - проблема в учетных данных');
                            Log::error('');
                            Log::error('ШАГ 2: Попробуйте TLS вместо SSL (РЕКОМЕНДУЕТСЯ ПЕРВЫМ)');
                            Log::error('  В .env файле установите:');
                            Log::error('  MAIL_PORT=2525');
                            Log::error('  MAIL_ENCRYPTION=tls');
                            Log::error('  Затем: php artisan config:clear');
                            Log::error('');
                            Log::error('ШАГ 3: Если TLS не работает, попробуйте SSL');
                            Log::error('  В .env файле установите:');
                            Log::error('  MAIL_PORT=465');
                            Log::error('  MAIL_ENCRYPTION=ssl');
                            Log::error('  Затем: php artisan config:clear');
                            Log::error('');
                            Log::error('ШАГ 4: Проверьте формат пароля в .env');
                            Log::error('  Пароль должен быть БЕЗ кавычек: MAIL_PASSWORD=ENBSD!27XUqwe');
                            Log::error('  НЕ используйте: MAIL_PASSWORD="ENBSD!27XUqwe"');
                            Log::error('  Проверьте, нет ли пробелов в начале или конце');
                            Log::error('');
                            Log::error('ШАГ 5: Если пароль содержит специальные символы (!, @, #, и т.д.)');
                            Log::error('  Попробуйте изменить пароль в панели SpaceWeb на пароль БЕЗ специальных символов');
                            Log::error('  Или используйте только буквы, цифры и дефис/подчеркивание');
                        } elseif (str_contains($errorMessage, 'Connection') || str_contains($errorMessage, 'timeout')) {
                            Log::error('❌ ПРОБЛЕМА С ПОДКЛЮЧЕНИЕМ К SMTP СЕРВЕРУ');
                            Log::error('Текущие настройки:');
                            Log::error('  - HOST: ' . config('mail.mailers.smtp.host'));
                            Log::error('  - PORT: ' . config('mail.mailers.smtp.port'));
                            Log::error('  - ENCRYPTION: ' . config('mail.mailers.smtp.encryption'));
                            Log::error('Проверьте доступность SMTP сервера и правильность настроек');
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
            
            // Проверяем, пытались ли мы отправить письмо (проверка по логам не нужна для пользователя)
            // Пользователю всегда говорим, что данные обновлены успешно
            // Проблемы с отправкой письма логируются отдельно

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
