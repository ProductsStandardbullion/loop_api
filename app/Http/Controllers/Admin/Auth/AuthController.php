<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    //public $baseURL = "https://www.a17df33bf3894184bed0f160b4cee9b2.loopoptions.com/api/v1/";
    public $baseURL = "http://localhost:8000/api/v1/";
    public function index()
    {
        $data['title'] = 'HQ login';

        return view('admin.auth.index', ['data' => collect($data)]);
    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('hq')->with('success', 'Good to see you again, '.  auth()->user()->first_name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
