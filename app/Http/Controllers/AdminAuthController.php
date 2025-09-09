<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
     public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['login'=>'Credenciales inválidas']);
        }
        session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
        return redirect()->route('admin.panel');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id','admin_name']);
        return redirect()->route('home');
    }
}
