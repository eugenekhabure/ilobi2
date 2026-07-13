<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->hasTwoFactorEnabled()) {
            session([
                '2fa:user_id' => $user->id,
                '2fa:remember' => $request->has('remember'),
            ]);
            Auth::logout();
            return redirect()->route('2fa.verify');
        }

        return redirect()->intended('/admin/dashboard');
    }

    public function username()
    {
        return 'email';
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}