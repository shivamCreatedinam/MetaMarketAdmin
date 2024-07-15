<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginView()
    {
        return view("auth.login");
    }
    public function loginPost(Request $request)
    {
        $request->validate([
            "email" => "required|email|exists:users,email",
            "password" => "required",
        ]);

        $credentials = $request->only(["email", "password"]);
        try {
            if (Auth::attempt($credentials)) {
                return redirect()->route('dashboard')->with("success", "Login Successfull.");
            } else {
                return redirect()->back()->with("error", "Password Incorrect or Some Error Occured.");
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with("success", "Logout Successfully!");
    }
}
