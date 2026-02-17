<?php

namespace App\Http\Controllers;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LoginController extends Controller
{
    public function showOTP() {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login');
        }
        return view('LoginPage.otp');
    }
    public function verifyOTP(Request $request) {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login');
        }
        $request->validate([
            'otp' => 'required'
        ]);
        $user = Auth::user();
        #dd($user); // <-- Add this line
        if ($user->otp !== $request->otp) {
            return back()->withErrors(['OTP' => 'invalid otp']);
        }
        $user->status = 'verified';
        $user->otp = 'NULL';
        $user->save();



    }
    public function showRegister() {
        return view('LoginPage.register');
    }
    public function register(Request $request) {
        $randomNumber = mt_rand(100000, 999999);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'otp' => $randomNumber,
            'password' => Hash::make($request->password),
        ]);
        return redirect('/verify-otp-form')->with('success', 'Account created!');
    }
    public function showLogin(): \Illuminate\View\View
    {
        return view('LoginPage.login');
    }
    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->status === 'suspended') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended.']);
            }
            if (Auth::user()->role === 'admin' || Auth::user()->role === 'Admin' || Auth::user()->role === 'ADMIN') {
                return redirect()->intended('/admin/users');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
    public function logout() {
        Auth::logout();
        return to_route('logout');
    }
}
