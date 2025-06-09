<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch language for frontend only
     */
    public function switch(Request $request, $locale)
    {
        // List of supported languages
        $supportedLocales = ['en', 'vi'];
        
        if (in_array($locale, $supportedLocales)) {
            Session::put('locale', $locale);
        }
        
        return redirect()->back();
    }
    
    /**
     * Get current translations for frontend only
     */
    public function getTranslations()
    {
        $locale = app()->getLocale();
        
        $translations = [
            'auth' => trans('auth', [], $locale) ?: [],
            // Frontend translations only
            'frontend' => [
                'common' => trans('frontend.common', [], $locale) ?: [],
                'product' => trans('frontend.product', [], $locale) ?: [],
                'cart' => trans('frontend.cart', [], $locale) ?: [],
                'checkout' => trans('frontend.checkout', [], $locale) ?: [],
            ],
        ];
        
        return response()->json($translations);
    }
} 