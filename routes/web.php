<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Staff\TicketController as StaffTicketController;
use App\Http\Controllers\IT\TicketController as ItTicketController;
use App\Http\Middleware\RememberMeMiddleware;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\IT\ManageUserController;
use App\Http\Controllers\IT\DepartmentController as ItDepartmentController;

// -----------------------------
// PUBLIC LOGIN ROUTES
// -----------------------------
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// -----------------------------
// CHANGE PASSWORD (Universal - untuk semua role)
// -----------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'showForm'])->name('password.form');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

// -----------------------------
// DASHBOARD dengan RoleRedirect
// -----------------------------
Route::middleware(['auth', RememberMeMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard')
        ->middleware('roleredirect:admin');
    Route::get('/it/dashboard', [DashboardController::class, 'it'])->name('it.dashboard')
        ->middleware('roleredirect:tim it');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])->name('staff.dashboard')
        ->middleware('roleredirect:staff,user');
});

// -----------------------------
// STAFF ROUTES dengan RoleRedirect
// -----------------------------
Route::middleware(['auth', 'roleredirect:staff,user'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tickets', [StaffTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [StaffTicketController::class, 'store'])->name('tickets.store');

    // ✅ DETAIL TICKET
    Route::get('/tickets/{id}', [StaffTicketController::class, 'show'])->name('tickets.show');
    // Di routes/web.php atau staff routes
    Route::delete('/tickets/{id}', [StaffTicketController::class, 'destroy'])->name('staff.tickets.destroy');
    // Dashboard small tickets
    Route::get('/fetch-dashboard-tickets', [StaffTicketController::class, 'fetchDashboardTickets'])->name('tickets.fetchDashboard');

    // Di routes/web.php dalam staff group:
    Route::post('/tickets/{id}/feedback', [StaffTicketController::class, 'storeFeedback'])->name('tickets.feedback.store');
    Route::get('/tickets/{id}/feedback', [StaffTicketController::class, 'getFeedback'])->name('tickets.feedback.show');
    
    // PROFILE ROUTES
    Route::get('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
    
    // Redirect old change password route to profile
    Route::get('/change-password', function() {
        return redirect()->route('staff.profile');
    })->name('password.form');
});

// -----------------------------
// IT ROUTES dengan RoleRedirect
// -----------------------------
Route::middleware(['auth', 'roleredirect:tim it'])->prefix('it')->name('it.')->group(function () {
    
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
    
    // 🚚 TRANSFER ROUTES
    Route::get('/locations/{location}/staff', [ItTicketController::class, 'getStaffByLocation'])->name('locations.staff');
    Route::post('/tickets/{ticket}/transfer', [ItTicketController::class, 'transfer'])->name('tickets.transfer');
    
    // PROFILE ROUTES (includes password change)
    Route::get('/profile', [App\Http\Controllers\IT\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\IT\ProfileController::class, 'update'])->name('profile.update');
    
    // Redirect old change password route to profile
    Route::get('/change-password', function() {
        return redirect()->route('it.profile');
    })->name('password.form');

     // ⭐ FEEDBACK ROUTES
    Route::get('/feedbacks', [App\Http\Controllers\IT\FeedbackController::class, 'index'])
        ->name('feedbacks.index');
    Route::get('/feedbacks/stats', [App\Http\Controllers\IT\FeedbackController::class, 'getStats'])
        ->name('feedbacks.stats');
    Route::get('/feedbacks/list', [App\Http\Controllers\IT\FeedbackController::class, 'getFeedbacksList'])
        ->name('feedbacks.list');
    Route::get('/feedbacks/export', [App\Http\Controllers\IT\FeedbackController::class, 'exportExcel'])
        ->name('feedbacks.export');
    Route::get('/feedbacks/{id}', [App\Http\Controllers\IT\FeedbackController::class, 'show'])
        ->name('feedbacks.show');
});

// -----------------------------
// ADMIN ROUTES dengan RoleRedirect
// -----------------------------
Route::middleware(['auth', 'roleredirect:admin'])->prefix('admin')->name('admin.')->group(function () {
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
    
    // ✅ LOCATION ROUTES
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
});

// -----------------------------
// DEBUG ROUTE (Temporary)
// -----------------------------
Route::get('/test-email-notification', function () {
    // SECURITY: Disabled for production
    abort(404);
});

// -----------------------------
// DEBUG IT STAFF (Who receives emails?)
// -----------------------------
// -----------------------------
// DEBUG IT TARGET (Who are these users?)
// -----------------------------
Route::get('/debug-it-target', function () {
    // SECURITY: Disabled for production
    abort(404);
});

// -----------------------------
// FALLBACK ROUTES untuk handle redirect
// -----------------------------
Route::fallback(function () {
    if (Auth::check()) {
        $user = Auth::user();
        $role = strtolower($user->role);
        
        $redirectRoutes = [
            'admin' => 'admin.dashboard',
            'tim it' => 'it.dashboard',
            'staff' => 'staff.dashboard',
            'user' => 'staff.dashboard',
        ];
        
        $route = $redirectRoutes[$role] ?? 'login';
        return redirect()->route($route)->with('warning', 'Halaman tidak ditemukan.');
    }
    
    return redirect()->route('login');
});