<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch language
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
     * Get current translations for frontend
     */
    public function getTranslations()
    {
        $locale = app()->getLocale();
        
        $translations = [
            'auth' => trans('auth', [], $locale) ?: [],
            'admin' => [
                'auth' => trans('admin.auth', [], $locale) ?: [],
                'dashboard' => trans('admin.dashboard', [], $locale) ?: [],
                'nav' => trans('admin.nav', [], $locale) ?: [],
            ],
        ];
        
        return response()->json($translations);
    }
} 