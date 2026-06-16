<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::redirect('/relatorios', '/admin/relatorios-reservas')->name('relatorios');
