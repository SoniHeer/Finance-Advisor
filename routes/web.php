<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\User\{
    DashboardController,
    IncomeController,
    ExpenseController,
    FamilyController,
    ProfileController,
    ReportController,
    AiChatController,
    NotificationController
};

use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    UserController as AdminUserController,
    SecurityController as AdminSecurityController
};

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/features', 'pages.features')->name('features');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/about', 'pages.about')->name('about');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

Route::view('/contact', 'pages.contact')->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| AUTH (Guest Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::controller(AuthController::class)->group(function () {

        Route::get('/login', 'loginPage')->name('login');
        Route::post('/login', 'login')->name('login.attempt');

        Route::get('/register', 'registerPage')->name('register');
        Route::post('/register', 'register')->name('register.store');
    });

});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | USER PANEL
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')
        ->as('user.')
        ->scopeBindings()
        ->group(function () {

            /* Dashboard */
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            /* Expenses */
            Route::resource('expenses', ExpenseController::class);
            Route::get('expenses/export/pdf',
                [ExpenseController::class, 'exportPdf'])
                ->name('expenses.export.pdf');

            /* Incomes */
            Route::resource('incomes', IncomeController::class);

            /* Families */
            Route::resource('families', FamilyController::class)
                ->only(['index','create','store','show']);

            Route::post('families/{family}/invite',
                [FamilyController::class, 'invite'])
                ->middleware('throttle:5,1')
                ->name('families.invite');

            Route::post('families/{family}/accept/{token}',
                [FamilyController::class, 'acceptInvite'])
                ->name('families.accept');

            /* AI Chat */
            Route::prefix('ai')->as('ai.')->group(function () {

                Route::get('/chat',
                    [AiChatController::class, 'index'])
                    ->name('chat');

                Route::post('/chat/send',
                    [AiChatController::class, 'sendMessage'])
                    ->middleware('throttle:30,1')
                    ->name('chat.send');
            });

            /* Notifications */
            Route::get('/notifications',
                [NotificationController::class, 'index'])
                ->name('notifications.index');

            Route::post('/notifications/{notification}/read',
                [NotificationController::class, 'markAsRead'])
                ->name('notifications.read');

            /* Reports */
            Route::middleware('role:admin,manager')
                ->get('/reports',
                    [ReportController::class, 'index'])
                ->name('reports.index');

            /* Profile */
            Route::prefix('profile')
                ->as('profile.')
                ->controller(ProfileController::class)
                ->group(function () {

                    Route::get('/', 'index')->name('index');
                    Route::get('/edit', 'edit')->name('edit');
                    Route::post('/update', 'update')->name('update');

                    Route::get('/password', 'passwordForm')
                        ->name('password.form');

                    Route::post('/password', 'updatePassword')
                        ->name('password.update');

                    Route::view('/subscription',
                        'user.subscription.index')
                        ->name('subscription');
                });

        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->middleware('admin')
        ->as('admin.')
        ->group(function () {

            Route::get('/dashboard',
                [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/users',
                [AdminUserController::class, 'index'])
                ->name('users.index');

            Route::patch('/users/{user}/block',
                [AdminUserController::class, 'block'])
                ->name('users.block');

            Route::delete('/users/{user}',
                [AdminUserController::class, 'destroy'])
                ->name('users.destroy');

            Route::get('/activities',
                [AdminSecurityController::class, 'index'])
                ->name('activities.index');

        });

});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('errors.404');
});
