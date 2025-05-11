<?php

use Illuminate\Support\Facades\Route;
use Koneko\VuexyAdmin\Application\Http\Controllers\LanguageController;

Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('language.swap');
