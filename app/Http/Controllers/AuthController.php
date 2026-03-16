<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* =========================
       SHOW LOGIN PAGE
    ========================== */
    public function loginPage()
    {
        if (Auth::check() && ! session()->has('invite_token')) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    /* =========================
       SHOW REGISTER PAGE
    ========================== */
    public function registerPage()
    {
        if (Auth::check() && ! session()->has('invite_token')) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.register');
    }

    /* =========================
       REGISTER NEW USER
    ========================== */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Continue invite flow
        if (session()->has('invite_token')) {
            $token = session()->pull('invite_token');

            return redirect()->route('user.families.accept', [
                'family' => 1, // Replace properly if needed
                'token'  => $token
            ]);
        }

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Registration successful.');
    }

    /* =========================
       LOGIN USER
    ========================== */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials['email'] = strtolower($credentials['email']);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Continue invite flow
        if (session()->has('invite_token')) {
            $token = session()->pull('invite_token');

            return redirect()->route('user.families.accept', [
                'family' => 1, // Replace properly if needed
                'token'  => $token
            ]);
        }

        if (! empty($user->role) && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    /* =========================
       LOGOUT USER
    ========================== */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
