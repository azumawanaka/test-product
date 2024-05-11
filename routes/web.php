<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });

Route::get('/login', LoginController::class);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', RegisterController::class);
Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Users
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::get('/api/check-email', [UserController::class, 'checkEmail']);
    Route::delete('/api/users/{user}', [UserController::class, 'destroy']);
    Route::get('/api/users/search', [UserController::class, 'search']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);

    // Products
    Route::get('/api/products', [ProductController::class, 'index']);
    Route::post('/api/products', [ProductController::class, 'store']);
    Route::get('/api/products/{product}/show', [ProductController::class, 'show']);
    Route::put('/api/products/{product}', [ProductController::class, 'update']);
    Route::delete('/api/products/{product}', [ProductController::class, 'destroy']);
    Route::delete('/api/products', [ProductController::class, 'bulkDelete']);

    Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
});

