<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard routes
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/it/dashboard', [DashboardController::class, 'it'])->name('it.dashboard');
Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');

