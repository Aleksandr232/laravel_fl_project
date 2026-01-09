<?php


use App\Models\Catalog;
use App\Helpers\MenuHelper;
use App\Models\Seo;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('cp')
                ->group(base_path('routes/cp.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \App\Http\Middleware\Permission::class,
        ]);
        $middleware->append(\App\Http\Middleware\RedirectTo::class);
        
        // Настройка редиректа для неавторизованных пользователей
        $middleware->redirectGuestsTo(route('frontend.index'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $seo = Seo::where('type', 'frontend.catalog')->first();
            $title = '404';
            $meta_description = 'АСТ Компонентс поставляет электронные компоненты для вашего бизнеса';
            $meta_keywords = '';
            $meta_title = '';
            $seo_url_canonical = '';
            $h1 = $seo->h1 ?? $title;
            $menu = MenuHelper::getMenuList();
            $catalogsList = Catalog::getCatalogList();
            $catalogs = Catalog::orderBy('name')->where('parent_id', 0)->get();

            return response()->view('errors.404', compact(
                'meta_description',
                'meta_keywords',
                'meta_title',
                'menu',
                'catalogs',
                'catalogsList',
                'h1',
                'seo_url_canonical',
                'title'
            ), 404);
        });
    })->create();

