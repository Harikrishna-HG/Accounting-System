<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, ['en', 'ne'], true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        return back();
    }
}
