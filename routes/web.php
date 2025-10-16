<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\It\TicketController as ItTicketController;
use App\Http\Middleware\RememberMeMiddleware;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\It\ManageUserController;
use App\Http\Controllers\It\DepartmentController as ItDepartmentController;

// -----------------------------
// PUBLIC LOGIN ROUTES
// -----------------------------
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// -----------------------------
// CHANGE PASSWORD
// -----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('password.form');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

// -----------------------------
// DASHBOARD
// -----------------------------
Route::middleware(['auth', RememberMeMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/it/dashboard', [DashboardController::class, 'it'])->name('it.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');
});

// -----------------------------
// STAFF ROUTES
// -----------------------------
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tickets', [StaffTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [StaffTicketController::class, 'store'])->name('tickets.store');

    // ✅ DETAIL TICKET
    Route::get('/tickets/{id}', [StaffTicketController::class, 'show'])->name('tickets.show');

    // Dashboard small tickets
    Route::get('/fetch-dashboard-tickets', [StaffTicketController::class, 'fetchDashboardTickets'])->name('tickets.fetchDashboard');
});

// -----------------------------
// IT ROUTES
// -----------------------------
Route::middleware(['auth', 'role:it'])->prefix('it')->name('it.')->group(function () {
    Route::resource('news', NewsController::class);
    Route::post('/departments', [ItDepartmentController::class, 'store'])->name('departments.store');
    Route::get('/staff', [ManageUserController::class, 'index'])->name('staff.index');
    Route::get('/staff/{id}', [ManageUserController::class, 'show'])->name('staff.show');
    Route::post('/staff', [ManageUserController::class, 'store'])->name('staff.store');
    Route::put('/staff/{id}', [ManageUserController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [ManageUserController::class, 'destroy'])->name('staff.destroy');
    Route::resource('tickets', ItTicketController::class)->only(['index','store','show','update']);
    Route::get('/tickets-history', [ItTicketController::class, 'riwayat'])->name('tickets.history');
    Route::post('/tickets/{ticket}/update-field', [ItTicketController::class, 'updateField'])->name('tickets.updateField');
});

// -----------------------------
// ADMIN ROUTES
// -----------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'showUsers'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}', [AdminController::class, 'getUser'])->name('users.show');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/export/pdf', [AdminTicketController::class, 'exportPdf'])->name('tickets.export.pdf');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}', [AdminController::class, 'getCategory'])->name('categories.show');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');
    Route::post('/export/csv', [AdminController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [AdminTicketController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('chart.data');
});
