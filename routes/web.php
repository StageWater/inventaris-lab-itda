<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuanganController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('ruangan', RuanganController::class);