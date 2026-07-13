<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PwaController extends Controller
{
    /**
     * Show the PWA main page
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('pwa.dashboard');
        }
        return view('pwa.index');
    }

    /**
     * Show the PWA login page
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('pwa.dashboard');
        }
        return view('pwa.login');
    }

    /**
     * Handle PWA login
     */
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('pwa.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the PWA dashboard (role-based)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $role = $user->getrole->name ?? 'User';
        
        // Determine which dashboard to show
        if ($role == 'Security') {
            return view('pwa.dashboard-security');
        } elseif ($role == 'Employee') {
            return view('pwa.dashboard-employee');
        } elseif ($role == 'Resident') {
            return view('pwa.dashboard-resident');
        } else {
            return view('pwa.dashboard-user');
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('pwa.login');
    }

    /**
     * Load dynamic pages (SPA-like navigation)
     */
    public function loadPage($page)
    {
        $user = Auth::user();
        $role = $user->getrole->name ?? 'User';

        // Determine which pages are allowed based on role
        $allowedPages = ['dashboard', 'visitors', 'scan', 'history', 'profile'];
        
        if (!in_array($page, $allowedPages)) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        // Return the appropriate view
        $view = 'pwa.pages.' . $page;
        return view($view, compact('user', 'role'));
    }
}