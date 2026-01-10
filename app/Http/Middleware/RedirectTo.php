<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class RedirectTo
{
    /**
     * Creates a new instance of the middleware.
     *
     */
    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Проверяем, что APP_KEY установлен (для работы с сессиями/шифрованием)
            if (empty(config('app.key'))) {
                // Если ключ не установлен, пропускаем проверку редиректов
                return $next($request);
            }

            // Проверяем, что таблица существует (с обработкой ошибок подключения к БД)
            try {
                if (!Schema::hasTable('redirect')) {
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Если база данных недоступна, продолжаем без редиректа
                Log::debug('RedirectTo middleware: база данных недоступна или таблица не существует');
                return $next($request);
            }

            $currentURL = request()->path();
            
            // Проверяем наличие редиректа для текущего URL
            try {
                $redirect = Redirect::where('from', $currentURL)->first();

                if ($redirect && $redirect->to) {
                    $statusCode = $redirect->status ? (int)$redirect->status : 301;
                    return redirect()->to($redirect->to, $statusCode);
                }
            } catch (\Exception $e) {
                // Если произошла ошибка при запросе, продолжаем без редиректа
                Log::warning('RedirectTo middleware: ошибка при проверке редиректа: ' . $e->getMessage());
                return $next($request);
            }
        } catch (\Illuminate\Encryption\MissingAppKeyException $e) {
            // Если ключ шифрования не установлен
            Log::warning('RedirectTo middleware: APP_KEY не установлен');
            return $next($request);
        } catch (\Exception $e) {
            // В случае любой ошибки просто продолжаем выполнение запроса
            Log::warning('RedirectTo middleware: общая ошибка: ' . $e->getMessage());
        }
        
        return $next($request);
    }
}