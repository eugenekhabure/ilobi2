<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
        $this->middleware('auth');
    }

    /**
     * Show the 2FA setup page.
     */
    public function showSetup()
    {
        $user = Auth::user();
        
        // Generate a new secret if not already set
        if (empty($user->two_factor_secret)) {
            $secret = $this->google2fa->generateSecretKey();
            $user->two_factor_secret = $secret;
            $user->save();
        }

        $qrCode = $this->google2fa->getQRCodeInline(
            config('app.name', 'ILOBI'),
            $user->email,
            $user->two_factor_secret
        );

        return view('auth.2fa.setup', compact('qrCode'));
    }

    /**
     * Confirm 2FA setup.
     */
    public function confirmSetup(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = $user->two_factor_secret;

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        // Confirm 2FA
        $user->enableTwoFactor($secret);
        $user->generateRecoveryCodes();
        $user->two_factor_confirmed_at = now();
        $user->save();

        $recoveryCodes = $user->getRecoveryCodes();

        return redirect()->route('2fa.backup-codes')
            ->with('success', 'Two-factor authentication has been enabled successfully!')
            ->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Show the backup codes.
     */
    public function showBackupCodes()
    {
        $user = Auth::user();
        $recoveryCodes = $user->getRecoveryCodes();

        if (empty($recoveryCodes)) {
            return redirect()->route('2fa.setup');
        }

        return view('auth.2fa.backup-codes', compact('recoveryCodes'));
    }

    /**
     * Regenerate backup codes.
     */
    public function regenerateBackupCodes()
    {
        $user = Auth::user();
        $user->generateRecoveryCodes();
        $recoveryCodes = $user->getRecoveryCodes();

        return redirect()->route('2fa.backup-codes')
            ->with('success', 'New backup codes generated successfully!')
            ->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        // Verify password
        if (!Auth::validate(['email' => $user->email, 'password' => $request->password])) {
            return back()->withErrors([
                'password' => 'The password is incorrect.',
            ]);
        }

        $user->disableTwoFactor();

        return redirect()->route('profile')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show the 2FA verification page (for login).
     */
    public function showVerify()
    {
        if (!session('2fa:user_id')) {
            return redirect()->route('login');
        }

        $userId = session('2fa:user_id');
        $user = \App\Models\User::find($userId);

        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        return view('auth.2fa.verify');
    }

    /**
     * Verify the 2FA code during login.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa:user_id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if it's a recovery code
        if ($user->verifyRecoveryCode($request->code)) {
            Auth::loginUsingId($userId, session('2fa:remember', false));
            session()->forget('2fa:user_id');
            session()->forget('2fa:remember');
            return redirect()->intended('/admin/dashboard');
        }

        // Check if it's a valid TOTP
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        Auth::loginUsingId($userId, session('2fa:remember', false));
        session()->forget('2fa:user_id');
        session()->forget('2fa:remember');

        return redirect()->intended('/admin/dashboard');
    }

    /**
     * Resend the 2FA verification code (for backup).
     */
    public function resend()
    {
        return back()->with('info', 'Please use a new code from your authenticator app.');
    }
}