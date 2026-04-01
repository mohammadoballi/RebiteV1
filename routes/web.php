<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Donor\DashboardController as DonorDashboardController;
use App\Http\Controllers\Donor\DonationController as DonorDonationController;
use App\Http\Controllers\Charity\DashboardController as CharityDashboardController;
use App\Http\Controllers\Charity\DonationController as CharityDonationController;
use App\Http\Controllers\Volunteer\DashboardController as VolunteerDashboardController;
use App\Http\Controllers\Volunteer\AssignmentController;
use App\Http\Controllers\Volunteer\DonationController as VolunteerDonationController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\LocationController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/how-it-works', function () {
    return view('pages.how-it-works');
})->name('how-it-works');

Route::get('/safety-guidelines', function () {
    return view('pages.safety-guidelines');
})->name('safety-guidelines');
Route::get('/home', function () {
  $role = auth()->user()->roles->pluck('display_name')->implode(', ');
  if ($role == 'admin') {
    return redirect()->route('admin.dashboard');
  } elseif ($role == 'donor') {
    return redirect()->route('donor.dashboard');
  } elseif ($role == 'charity') {
    return redirect()->route('charity.dashboard');
  } elseif ($role == 'volunteer') {
    return redirect()->route('volunteer.dashboard');
  }
})->name('home');
// Language Switcher
Route::get('language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Location API (public, used by registration and other forms)
Route::get('api/cities', [LocationController::class, 'cities'])->name('api.cities');
Route::get('api/cities/{city}/towns', [LocationController::class, 'towns'])->name('api.towns');

// Stripe Webhook (excluded from CSRF)
Route::post('stripe/webhook', [\App\Http\Controllers\Charity\SubscriptionController::class, 'handleWebhook'])->name('stripe.webhook');

// Authenticated & Approved Routes
Route::middleware(['auth', 'approved'])->group(function () {

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::get('unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('datatable', [AdminUserController::class, 'datatable'])->name('datatable');
            Route::get('{id}', [AdminUserController::class, 'show'])->name('show');
            Route::put('{id}', [AdminUserController::class, 'update'])->name('update');
            Route::post('{id}/approve', [AdminUserController::class, 'approve'])->name('approve');
            Route::post('{id}/reject', [AdminUserController::class, 'reject'])->name('reject');
            Route::delete('{id}', [AdminUserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('/', [AdminDonationController::class, 'index'])->name('index');
            Route::get('datatable', [AdminDonationController::class, 'datatable'])->name('datatable');
            Route::get('{id}', [AdminDonationController::class, 'show'])->name('show');
            Route::put('{id}/status', [AdminDonationController::class, 'updateStatus'])->name('update-status');
            Route::delete('{id}', [AdminDonationController::class, 'destroy'])->name('destroy');
        });

        Route::get('reports', [AdminReportController::class, 'index'])->name('reports');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::post('/', [AdminSettingsController::class, 'update'])->name('update');
        });

        Route::prefix('donation-requests')->name('donation-requests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DonationRequestController::class, 'index'])->name('index');
            Route::get('datatable', [\App\Http\Controllers\Admin\DonationRequestController::class, 'datatable'])->name('datatable');
            Route::post('{id}/approve', [\App\Http\Controllers\Admin\DonationRequestController::class, 'approve'])->name('approve');
            Route::post('{id}/reject', [\App\Http\Controllers\Admin\DonationRequestController::class, 'reject'])->name('reject');
        });
    });

    // Donor Routes
    Route::middleware('role:donor')->prefix('donor')->name('donor.')->group(function () {
        Route::get('dashboard', [DonorDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('/', [DonorDonationController::class, 'index'])->name('index');
            Route::get('datatable', [DonorDonationController::class, 'datatable'])->name('datatable');
            Route::post('/', [DonorDonationController::class, 'store'])->name('store');
            Route::get('{id}', [DonorDonationController::class, 'show'])->name('show');
            Route::put('{id}', [DonorDonationController::class, 'update'])->name('update');
            Route::delete('{id}', [DonorDonationController::class, 'destroy'])->name('destroy');
        });
    });

    // Charity Routes
    Route::middleware('role:charity')->prefix('charity')->name('charity.')->group(function () {
        Route::get('dashboard', [CharityDashboardController::class, 'index'])->name('dashboard');

        // Subscription management (no subscription required)
        Route::get('subscription', [\App\Http\Controllers\Charity\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('subscription/checkout', [\App\Http\Controllers\Charity\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::get('subscription/success', [\App\Http\Controllers\Charity\SubscriptionController::class, 'success'])->name('subscription.success');

        // Browsing (no subscription required)
        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('/', [CharityDonationController::class, 'index'])->name('index');
            Route::get('datatable', [CharityDonationController::class, 'datatable'])->name('datatable');
            Route::get('{id}', [CharityDonationController::class, 'show'])->name('show');
        });

        Route::get('my-requests', [CharityDonationController::class, 'myRequests'])->name('my-requests');
        Route::get('my-requests/datatable', [CharityDonationController::class, 'myRequestsDatatable'])->name('my-requests.datatable');

        // Actions (subscription required)
        Route::middleware('subscribed')->group(function () {
            Route::post('donations/{id}/request', [CharityDonationController::class, 'request'])->name('donations.request');
        });
    });

    // Rating Routes (charity rates donor & volunteer after completion)
    Route::middleware('role:charity')->group(function () {
        Route::post('ratings', [RatingController::class, 'store'])->name('ratings.store');
    });
    Route::get('my-ratings', [RatingController::class, 'myRatings'])->name('ratings.my');

    // Volunteer Routes
    Route::middleware('role:volunteer')->prefix('volunteer')->name('volunteer.')->group(function () {
        Route::get('dashboard', [VolunteerDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('donations')->name('donations.')->group(function () {
            Route::get('/', [VolunteerDonationController::class, 'index'])->name('index');
            Route::get('{id}', [VolunteerDonationController::class, 'show'])->name('show');
            Route::post('{id}/assign', [VolunteerDonationController::class, 'selfAssign'])->name('assign');
        });

        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', [AssignmentController::class, 'index'])->name('index');
            Route::get('datatable', [AssignmentController::class, 'datatable'])->name('datatable');
            Route::get('{id}', [AssignmentController::class, 'show'])->name('show');
            Route::post('{id}/accept', [AssignmentController::class, 'accept'])->name('accept');
            Route::put('{id}/status', [AssignmentController::class, 'updateStatus'])->name('update-status');
            Route::post('{id}/pickup', [AssignmentController::class, 'markPickedUp'])->name('pickup');
            Route::post('{id}/deliver', [AssignmentController::class, 'markDelivered'])->name('deliver');
        });

        Route::get('my-ratings', function () {
            return view('volunteer.ratings.index');
        })->name('ratings');
    });
});
