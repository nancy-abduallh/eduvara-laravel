<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function setLocale($locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        session()->put('locale', $locale);
        app()->setLocale($locale);
        
        return redirect()->back();
    }
}