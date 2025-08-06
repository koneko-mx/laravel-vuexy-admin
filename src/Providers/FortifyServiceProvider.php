<?php

declare(strict_types=1);

namespace Koneko\VuexyAdmin\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\{ServiceProvider,Str};
use Illuminate\Support\Facades\{Hash, RateLimiter};
use Koneko\VuexyAdmin\Application\Auth\Actions\Fortify\{CreateNewUser, ResetUserPassword, UpdateUserPassword, UpdateUserProfileInformation};
use Koneko\VuexyAdmin\Application\Cache\Builders\KonekoAdminVarsBuilder;
use Koneko\VuexyAdmin\Models\User;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Schema;

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
        if (! Schema::hasTable('settings')) {
            return;
        }

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

        // Obtiene el modo de vista de autenticación
        $viewMode = config_m()->get('layout.vuexy.authViewMode', 'cover');

        // Configurar la vista del login
        Fortify::loginView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.login-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista del registro (si lo necesitas)
        Fortify::registerView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista de restablecimiento de contraseñas
        Fortify::requestPasswordResetLinkView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.forgot-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        Fortify::resetPasswordView(function ($request) use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.reset-password-{$viewMode}", ['pageConfigs' => $pageConfigs, 'request' => $request]);
        });

        // Vista de verificación de correo electrónico
        Fortify::verifyEmailView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.verify-email-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Vista de confirmación de contraseña
        Fortify::confirmPasswordView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.confirm-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });

        // Configurar la vista para la verificación de dos factores
        Fortify::twoFactorChallengeView(function () use ($viewMode) {
            $pageConfigs = ['myLayout' => 'blank'];

            view()->share(['_admin' => app(KonekoAdminVarsBuilder::class)->get()]);

            return view("vuexy-admin::auth.two-factor-challenge-{$viewMode}", ['pageConfigs' => $pageConfigs]);
        });
    }
}
