<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::resource('customers', CustomerController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);

Route::resource('users', UserController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);

// Serve prepared public-disk avatars without requiring a filesystem symlink.
Route::get('/avatars/{filename}', AvatarController::class)
    ->where('filename', '[a-f0-9-]{36}\.png')
    ->name('avatars.show');
