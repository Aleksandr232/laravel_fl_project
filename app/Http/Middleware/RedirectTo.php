<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            $currentURL = request()->path();
            
            // Проверяем, что база данных доступна и таблица существует
            try {
                $redirect = Redirect::where('from', $currentURL)->first();
            } catch (\Exception $e) {
                // Если таблица не существует или база недоступна, продолжаем без редиректа
                \Log::warning('RedirectTo middleware: ошибка при проверке редиректа: ' . $e->getMessage());
                return $next($request);
            }

            if ($redirect) {
                return redirect()->to($redirect->to, (int)$redirect->status);
            }
        } catch (\Exception $e) {
            // В случае любой ошибки просто продолжаем выполнение запроса
            \Log::warning('RedirectTo middleware: ошибка: ' . $e->getMessage());
        }
        
        return $next($request);
    }
}