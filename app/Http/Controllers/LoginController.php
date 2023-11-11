<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login()
    {
        request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $email = request('email');
        $user = User::where('email', $email)->first();
    
        if (Hash::check(request('password'), $user->password)) {
            Auth::login($user);
    
            return redirect('/dashboard');
        }
    
        return redirect('/login');
    }
}
