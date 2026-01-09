<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Helpers\MenuHelper;
use App\Models\Catalog;
use App\Models\Feedback;
use App\Models\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Страница профиля
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        // Проверяем авторизацию вручную, если middleware не сработал
        if (!Auth::guard('client')->check()) {
            return redirect()->route('frontend.index');
        }
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
}
