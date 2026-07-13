<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Set the application language.
     */
    public function switch($locale)
    {
        // Check if the language is supported
        $supportedLocales = ['en', 'sw', 'fr', 'de', 'zh'];
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        // Store the language in session
        Session::put('locale', $locale);
        
        // Set the application locale
        App::setLocale($locale);

        // Store in database if user is logged in
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }

        return redirect()->back();
    }

    /**
     * Get the current language.
     */
    public function getCurrent()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'languages' => $this->getLanguages()
        ]);
    }

    /**
     * Get supported languages.
     */
    public function getLanguages()
    {
        return [
            'en' => ['name' => 'English', 'flag' => '🇬🇧', 'code' => 'en'],
            'sw' => ['name' => 'Kiswahili', 'flag' => '🇰🇪', 'code' => 'sw'],
            'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'code' => 'fr'],
            'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪', 'code' => 'de'],
            'zh' => ['name' => '中文', 'flag' => '🇨🇳', 'code' => 'zh'],
        ];
    }
}