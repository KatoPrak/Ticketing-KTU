<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\It\TicketController as ItTicketController;
use App\Http\Middleware\RememberMeMiddleware;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\It\ManageUserController;


// ============================
// IT Manage Staff routes
// ============================
Route::middleware(['auth'])->prefix('it')->name('it.')->group(function () {
    Route::get('/staff', [ManageUserController::class, 'index'])->name('staff.index');
    Route::get('/staff/{id}', [ManageUserController::class, 'show'])->name('staff.show');
    Route::post('/staff', [ManageUserController::class, 'store'])->name('staff.store');
    Route::put('/staff/{id}', [ManageUserController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [ManageUserController::class, 'destroy'])->name('staff.destroy');
});


// ============================
// Login routes
// ============================
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('password.form');
Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');

// ============================
// Dashboard routes
// ============================
Route::middleware([RememberMeMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/it/dashboard', [DashboardController::class, 'it'])->name('it.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard');
});

// ============================
// Laporan staff
// ============================
Route::post('/report/submit', [ReportController::class, 'submit'])->name('report.submit');

// ============================
// Staff routes
// ============================
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tickets', [StaffTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [StaffTicketController::class, 'store'])->name('tickets.store');
    Route::get('/fetch-dashboard-tickets', [StaffTicketController::class, 'fetchDashboardTickets'])->name('tickets.fetchDashboard');
});

// ============================
// IT routes
// ============================
Route::middleware(['auth'])->prefix('it')->name('it.')->group(function () {
    // === NEWS ROUTE DIPINDAHKAN KE SINI ===
    Route::resource('news', NewsController::class);
    // ======================================

    Route::get('/index-ticket', [ItTicketController::class, 'index'])->name('index-ticket');
    Route::get('/tickets', [ItTicketController::class, 'index'])->name('it.tickets.index');
    Route::post('/tickets/{id}/update-field', [ItTicketController::class, 'updateField'])->name('it.tickets.update-field');
    Route::post('/tickets', [ItTicketController::class, 'store'])->name('tickets.store');
    Route::put('/tickets/{id}', [ItTicketController::class, 'update'])->name('tickets.update');
    Route::get('/riwayat-ticket', [ItTicketController::class, 'riwayat'])->name('riwayat-ticket');
    Route::get('/it/tickets/{ticket}', [App\Http\Controllers\It\TicketController::class, 'show'])->name('it.tickets.show');
    Route::get('/it/tickets/{id}', [App\Http\Controllers\It\TicketController::class, 'show'])->name('it.ticket.show');
    Route::get('/tickets/{ticket}', [ItTicketController::class, 'show'])->name('tickets.show');
});

// ============================
// Admin routes
// ============================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminController::class, 'showUsers'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}', [AdminController::class, 'getUser'])->name('users.show');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    // Tickets
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

    // Categories
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}', [AdminController::class, 'getCategory'])->name('categories.show');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.destroy');

    // Export
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');
    Route::post('/export/csv', [AdminController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [AdminController::class, 'exportPdf'])->name('export.pdf');
    Route::post('/export/pdf', [AdminController::class, 'exportPdf'])->name('export.pdf.post');

    // Chart
    Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('chart.data');
});