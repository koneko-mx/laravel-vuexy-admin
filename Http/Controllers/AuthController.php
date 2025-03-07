<?php

namespace Koneko\VuexyAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;

class AuthController extends Controller
{
    /*
    public function loginView()
    {
        dd($viewMode);
        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.login-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function registerView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }



    public function confirmPasswordView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.confirm-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function resetPasswordView()
    {
        if (!Features::enabled(Features::resetPasswords()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.reset-password-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function requestPasswordResetLinkView(Request $request)
    {
        if (!Features::enabled(Features::resetPasswords()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.reset-password-{$viewMode}", ['pageConfigs' => $pageConfigs, 'request' => $request]);
    }








    public function twoFactorChallengeView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.two-factor-challenge-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function twoFactorRecoveryCodesView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function twoFactorAuthenticationView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }





    public function verifyEmailView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.verify-email-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function showEmailVerificationForm()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }

    public function userProfileView()
    {
        if (!Features::enabled(Features::registration()))
            abort(403, 'El registro está deshabilitado.');

        $viewMode = config('vuexy.custom.authViewMode');
        $pageConfigs = ['myLayout' => 'blank'];

        return view("vuexy-admin::auth.register-{$viewMode}", ['pageConfigs' => $pageConfigs]);
    }
    */
}
