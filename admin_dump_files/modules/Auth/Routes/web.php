<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Modules\Auth\Http\Controllers\AuthenticatedSessionController;
use Modules\Auth\Http\Controllers\BrowserSessionsController;
use Modules\Auth\Http\Controllers\ConfirmablePasswordController;
use Modules\Auth\Http\Controllers\ConfirmedPasswordStatusController;
use Modules\Auth\Http\Controllers\EmailVerificationNotificationController;
use Modules\Auth\Http\Controllers\EmailVerificationPromptController;
use Modules\Auth\Http\Controllers\NewPasswordController;
use Modules\Auth\Http\Controllers\PasswordController;
use Modules\Auth\Http\Controllers\SettingController;
use Modules\Auth\Http\Controllers\PasswordResetLinkController;
use Modules\Auth\Http\Controllers\ProfileInformationController;
use Modules\Auth\Http\Controllers\RegisteredUserController;
use Modules\Auth\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Modules\Auth\Http\Controllers\TwoFactorAuthenticationController;
use Modules\Auth\Http\Controllers\VerifyEmailController;
use Modules\Auth\Http\Controllers\TicketModuleController;
use Modules\Auth\Http\Controllers\AuthDMController;

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    
    // Authentication...
    Route::get('/login', [AuthenticatedSessionController::class, 'index'])
        ->middleware(['guest:'.config('fortify.guard')])
        ->name('login');
        

    $limiter = config('fortify.limiters.login');
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');

    Route::post('/login', [AuthenticatedSessionController::class, 'attempt'])
        ->middleware(array_filter([
            'guest:'.config('fortify.guard'),
            $limiter ? 'throttle:'.$limiter : null,
        ]));

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'index'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.request');

        Route::get('/reset-password/{token}', [NewPasswordController::class, 'index'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.reset');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('password.update');
    }

    // Registration...
    if (Features::enabled(Features::registration())) {
        Route::get('/register', [RegisteredUserController::class, 'index'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('register');

        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')]);
            
    }
    
    


    // Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        Route::get('/email/verify', [EmailVerificationPromptController::class, 'index'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'signed', 'throttle:'.$verificationLimiter])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'throttle:'.$verificationLimiter])
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::get('/user/profile', [ProfileInformationController::class, 'index'])->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.index');

        Route::get('/user/profile-setting', [ProfileInformationController::class, 'general'])->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.general');

        Route::get('/user/profile-setting/edit', [ProfileInformationController::class, 'edit'])->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.edit');

        Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::get('/user/password', [PasswordController::class, 'index'])->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-password.index');

        Route::put('/user/password', [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-password.update');
    }

    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirmation');
    Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'index'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.index');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirm');

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'index'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('two-factor.login');

        Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:'.config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]));

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            // ? [config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard'), 'password.confirm']
            ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm']
            : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];

        Route::get('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'index'])->name('user-two-factor.index');

        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'enable'])
            ->middleware($twoFactorMiddleware)
            ->name('user-two-factor.enable');

        // Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        // 	->middleware($twoFactorMiddleware)
        // 	->name('two-factor.confirm');

        // Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        // 	->middleware($twoFactorMiddleware)
        // 	->name('two-factor.disable');

        // Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        // 	->middleware($twoFactorMiddleware)
        // 	->name('two-factor.qr-code');

        // Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
        // 	->middleware($twoFactorMiddleware)
        // 	->name('two-factor.secret-key');

        // Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
        // 	->middleware($twoFactorMiddleware)
        // 	->name('two-factor.recovery-codes');

        // Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        // 	->middleware($twoFactorMiddleware);
    }

    Route::get('/user/browser-session', [BrowserSessionsController::class, 'index'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('user-browser-sessions.index');

    Route::post('/user/browser-session/destroy', [BrowserSessionsController::class, 'destroy'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('user-browser-sessions.destroy');
        
     Route::get('/user/sms-settings', [PasswordController::class, 'sms_settings_view'])->name('sms_settings_view.index');
     Route::post('/user/sms-settings/update', [PasswordController::class, 'sms_settings_update'])->name('sms_settings_view.update');
     
     Route::get('/user/settings/app-version-manage', [SettingController::class, 'app_version_manage_view'])->name('app_version_manage.settings.index');
     Route::post('/user/settings/app-version-manage/update', [SettingController::class, 'app_version_manage_update'])->name('app_version_manage.settings.update');
     
     Route::post('/user/settings/agent-app-version-manage/update', [SettingController::class, 'updateAgentAppSettings'])->name('app_version_manage.settings.agent-update');
     Route::post('/user/settings/rider-app-version-manage/update', [SettingController::class, 'updateRiderAppSettings'])->name('app_version_manage.settings.rider-update');

     Route::get('/ticket-portal/login', [TicketModuleController::class, 'login']);
    
});


Route::get('/b2b/login', [AuthDMController::class, 'b2b_login_view'])->name('b2b_login_view');
Route::post('/b2b/login', [AuthDMController::class, 'b2b_login_check'])->name('b2b_login_check');
Route::get('/b2b/logout', [AuthDMController::class, 'b2b_logout'])->name('b2b_logout');

Route::get('/hr-manager/login', [AuthDMController::class, 'hr_manager_login_view'])->name('hr_manager_login_view');
Route::post('/hr-manager/login', [AuthDMController::class, 'hr_manager_login_check'])->name('hr_manager_login_check');
Route::post('/user/ticket-portal/login', [AuthDMController::class, 'login'])->name('user.ticket_portal.login');
Route::post('/user/ticket-portal/logout', [AuthDMController::class, 'logout'])->name('user.ticket_portal.logout');

// Route::prefix('deliveryman')->name('deliveryman.')->group(function () {
//     Route::get('login', [\App\Http\Controllers\DeliveryMan\Auth\LoginController::class, 'showLoginForm'])->name('login');
//     Route::post('login', [\App\Http\Controllers\DeliveryMan\Auth\LoginController::class, 'login']);
//     Route::post('logout', [\App\Http\Controllers\DeliveryMan\Auth\LoginController::class, 'logout'])->name('logout');

//     Route::middleware('auth:deliveryman')->group(function () {
//         Route::get('dashboard', function () {
//             return 'Welcome DeliveryMan!';
//         })->name('dashboard');
//     });
// });
