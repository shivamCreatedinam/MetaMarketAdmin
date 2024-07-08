<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
        return view("dashboard");
    }

    public function printHeaders(Request $request)
    {
        $headers = $request->headers->all();
        dd($request->ip());
    }
}
