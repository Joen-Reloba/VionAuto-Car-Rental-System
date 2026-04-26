<?php

use App\Http\Controllers\AdminController\AdminCustomerController;
use App\Http\Controllers\AdminController\DashboardController;
use App\Http\Controllers\AdminController\ManageUserController;
use App\Http\Controllers\AdminController\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController\CustomerBookingController;
use App\Http\Controllers\CustomerController\NotificationController;
use App\Http\Controllers\CustomerController\CustomerPaymentController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\StaffController\ManageVehicleController;
use App\Http\Controllers\StaffController\BookingController;
use App\Http\Controllers\StaffController\PaymentController;
use App\Http\Controllers\StaffController\StaffCustomerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController\CustomerProfileController;

// Landing page (no authentication required)
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/landing', [LandingPageController::class, 'index'])->name('landing.alt');
Route::get('/customer/browse-all-vehicles', [LandingPageController::class, 'browseAllVehicles'])->name('customer.browse-all-vehicles');
Route::get('/customer/vehicle/{vehicleId}', [LandingPageController::class, 'viewVehicle'])->name('customer.view-vehicle');
Route::post('/customer/booking/store', [CustomerBookingController::class, 'store'])->name('customer.booking.store');


// Customer routes (protected)
Route::middleware(['auth'])->prefix('customer')->group(function () {
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('customer.bookings');
    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('customer.payments');
    Route::get('/payments/{payment}', [CustomerPaymentController::class, 'show'])->name('customer.payments.show');
    Route::post('/submit-payment', [CustomerPaymentController::class, 'submitPayment'])->name('customer.submit-payment');
    Route::get('/check-pending-payment/{booking}', [CustomerPaymentController::class, 'checkPendingPayment'])->name('customer.check-pending-payment');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('customer.notifications');
    Route::get('/notifications/test', [NotificationController::class, 'testCreateNotification'])->name('customer.notifications.test');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('customer.notifications.unread-count');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('customer.notifications.show');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('customer.notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('customer.notifications.mark-all-read');
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('customer.profile');
    Route::put('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('customer.profile.password');
    Route::put('/customer/profile/update', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', fn() => view('customer.customer_registration'))->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Admin routes (protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [ManageUserController::class, 'index'])->name('admin.users');
    Route::get('/users/add', fn() => view('admin.admin_add_user'))->name('admin.users.add');
    Route::post('/users', [ManageUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}', [ManageUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{user}/edit', fn($user) => view('admin.admin_update_user'))->name('admin.users.edit');
    Route::put('/users/{user}', [ManageUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [ManageUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('admin.customers');
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
});

//Staff routes (protected)
Route::middleware(['auth'])->prefix('staff')->group(function () {
    Route::get('/vehicles', [ManageVehicleController::class, 'index'])->name('staff.vehicles');
    Route::get('/vehicles/create', [ManageVehicleController::class, 'create'])->name('staff.vehicles.create');
    Route::post('/vehicles', [ManageVehicleController::class, 'store'])->name('staff.vehicles.store');
    Route::get('/vehicles/{vehicle}/edit', [ManageVehicleController::class, 'edit'])->name('staff.vehicles.edit');
    Route::put('/vehicles/{vehicle}', [ManageVehicleController::class, 'update'])->name('staff.vehicles.update');
    Route::delete('/vehicles/{vehicle}', [ManageVehicleController::class, 'destroy'])->name('staff.vehicles.destroy');
    Route::get('/bookings', [BookingController::class, 'index'])->name('staff.bookings');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('staff.bookings.show');
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('staff.bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('staff.bookings.reject');
    Route::post('/bookings/{booking}/start-rental', [BookingController::class, 'startRental'])->name('staff.bookings.start-rental');
    Route::post('/bookings/{booking}/return-vehicle', [BookingController::class, 'returnVehicle'])->name('staff.bookings.return-vehicle');
    Route::get('/payments', [PaymentController::class, 'index'])->name('staff.payments');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('staff.payments.show');
    Route::post('/payments/approve', [PaymentController::class, 'approve'])->name('staff.payments.approve');
    Route::post('/payments/reject', [PaymentController::class, 'reject'])->name('staff.payments.reject');
    Route::get('/customers', [StaffCustomerController::class, 'index'])->name('staff.customers');
});