<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\LoginRequest;
use App\Http\Requests\Frontend\Auth\RegisterRequest;
use App\Http\Requests\Frontend\Auth\ForgotPasswordRequest;
use App\Http\Requests\Frontend\Auth\ResetPasswordRequest;
use App\Mail\PasswordRecoveryMail;
use App\Models\Clients;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\View\View;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Вы успешно авторизовались',
                'redirect' => route('frontend.profile.index'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Неверный email или пароль',
        ], 422);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $client = Clients::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('client')->login($client);

        return response()->json([
            'success' => true,
            'message' => 'Вы успешно зарегистрировались',
            'redirect' => route('frontend.profile.index'),
        ]);
    }

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Ссылка для сброса пароля отправлена на ваш email',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось отправить ссылку. Попробуйте позже.',
        ], 422);
    }

    /**
     * Восстановление пароля - генерация нового 12-символьного пароля и отправка на email
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function recoverPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // Находим пользователя по email
            $client = Clients::where('email', $request->email)->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким email не найден',
                ], 422);
            }

            // Генерируем новый безопасный пароль из 12 символов
            // Используем пароль с гарантированными типами символов (заглавные, строчные, цифры, спецсимволы)
            $newPassword = $this->generateSecurePassword(12);

            Log::info('Восстановление пароля', [
                'email' => $client->email,
                'user_id' => $client->id
            ]);

            // Обновляем пароль пользователя
            $client->password = Hash::make($newPassword);
            $client->save();

            Log::info('Пароль обновлен для восстановления', [
                'email' => $client->email
            ]);

            // Отправляем письмо с новым паролем
            try {
                Mail::to($client->email)->send(new PasswordRecoveryMail($client, $newPassword));
                
                Log::info('Письмо с новым паролем отправлено', [
                    'email' => $client->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Новый пароль отправлен на указанный email',
                ]);
            } catch (\Exception $e) {
                Log::error('Ошибка отправки письма при восстановлении пароля: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Пароль изменен, но не удалось отправить письмо. Обратитесь в службу поддержки.',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Ошибка при восстановлении пароля: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при восстановлении пароля. Попробуйте позже.',
            ], 500);
        }
    }

    /**
     * Генерация безопасного пароля с гарантированными типами символов
     *
     * @param int $length
     * @return string
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*';
        
        $allChars = $uppercase . $lowercase . $numbers . $special;
        
        // Гарантируем минимум по одному символу каждого типа
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Заполняем остаток случайными символами
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Перемешиваем символы
        return str_shuffle($password);
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('frontend.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('frontend.index')
                ->with('success', 'Пароль успешно изменен. Теперь вы можете войти.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Проверяем, является ли запрос ajax запросом
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Вы успешно вышли',
                'redirect' => route('frontend.index'),
            ]);
        }

        return redirect()->route('frontend.index');
    }
}
