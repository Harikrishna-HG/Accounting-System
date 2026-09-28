<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserAuthController extends Controller
{
    //
    public function login(Request $request)
    {

           if($request->isMethod('post')) {
             // validate the request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
                // 'remember' => 'boolean',
            ]);
            $email = $request->input('email');
            $password = $request->input('password');
            $remember = $request->input('remember');
            $user=User::where('email',$email)->first();
            // return ['auth' => auth()];
            if(!auth()->attempt(['email' => $email, 'password' => $password], $remember)) {
                // show session error message
                // session(['error' => 'Credentials do not match our records.']);
                return redirect()->back()->with('error', 'Credentials do not match our records.')->withInput();

            }
            // handle remember me functionality
            auth()->login($user, $remember);
            return redirect()->route('accounting.dashboard');
            
            // return ['request' => $request->all(),'user' => $user];
           }else {

               return view('auth.login');
           }

    }

    public function logout(Request $request)
    {
        auth()->logout();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
    public function register(Request $request)
    {
        if($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            auth()->login($user);

            return redirect()->route('accounting.dashboard')->with('success', 'Account created successfully!');
        }

        return view('auth.signup');
    }
    public function forgot_password()
    {
        return view('auth.forgot_password');
    }
}
