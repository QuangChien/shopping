<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $isAdminRoute = str_starts_with($request->path(), 'admin');
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'locale' => $isAdminRoute ? 'en' : app()->getLocale(),
            'translations' => $isAdminRoute ? [] : [
                'auth' => trans('auth'),
                // Frontend translations only
                'frontend' => [
                    'common' => trans('frontend.common'),
                    'product' => trans('frontend.product'),
                    'cart' => trans('frontend.cart'),
                    'checkout' => trans('frontend.checkout'),
                ],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ]);
    }
}
