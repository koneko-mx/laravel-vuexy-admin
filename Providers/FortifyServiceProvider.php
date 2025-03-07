<?php

namespace Koneko\VuexyAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Koneko\VuexyAdmin\Actions\Fortify\CreateNewUser;
use Koneko\VuexyAdmin\Actions\Fortify\ResetUserPassword;
use Koneko\VuexyAdmin\Actions\Fortify\UpdateUserPassword;
use Koneko\VuexyAdmin\Actions\Fortify\UpdateUserProfileInformation;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Services\AdminTemplateService;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)
                ->where('status', User::STATUS_ENABLED)
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        // Simula lo que hace tu middleware y comparte `_admin`
        $viewMode  = Config::get('vuexy.custom.authViewMode');
        $adminVars = app(AdminTemplateService::class)->getAdminVars();

        // Configurar la vista del login
        Fortify::loginView(function () use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.login-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista del registro (si lo necesitas)
        Fortify::registerView(function () use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista de restablecimiento de contraseñas
        Fortify::requestPasswordResetLinkView(function () use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.forgot-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        Fortify::resetPasswordView(function ($request) use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.reset-password-{$viewMode}", ['pageConfigs' => $pageConfigs, 'request' => $request]);
        });

        // Vista de verificación de correo electrónico
        Fortify::verifyEmailView(function () use ($viewMode, $adminVars) {
            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.verify-email-{$viewMode}");
        });

        // Vista de confirmación de contraseña
        Fortify::confirmPasswordView(function () use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.confirm-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista para la verificación de dos factores
        Fortify::twoFactorChallengeView(function () use ($viewMode, $adminVars) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share('_admin', $adminVars);

            return view("vuexy-admin::auth.two-factor-challenge-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });
    }
}
