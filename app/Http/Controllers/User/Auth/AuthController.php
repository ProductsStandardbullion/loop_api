<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    
    public $baseURL = 'https://www.a17df33bf3894184bed0f160b4cee9b2.loopoptions.com/api/v1/user/';
    public function index(){
        $data['title'] = 'Login to your '. config('app.name'). ' account';
        $data['description'] = 'Welcome back. Login to your '. config('app.name'). ' account';
        return view('user.auth.login', ['data' => collect($data)]);
    }

    public function register(){
        $data['title'] = 'Create your '. config('app.name'). ' to get started';
        $data['description'] = 'Create your '. config('app.name'). ' to get started';
        return view('user.auth.register',['data' => collect($data)]);
    }

    public function reg(Request $request){
       // dd($request->all());
        $data =$this->validate($request,[
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'password' => 'required'

        ]);

        $response = Http::post($this->baseURL.'new',[
            'first_name' => ucfirst($data['first_name']),
            'last_name' => ucfirst($data['last_name']),
            'phone' => $data['phone'],
            'email' => strtolower($data['email']),
            'password' => $data['password']
        ]);
        $res =json_decode($response->getBody(),true);
       
        if(isset($res[0]['status']) && $res[0]['status'] == false){
            $errors = '';
            foreach ($res[0]['error'] as $subarray) {
                foreach ($subarray as $value) {
                    $errors .= $value. '. ';
                }
            }
        return back()->with('error', $errors);
        }elseif($res['status'] == true){
            //redirect to verify account

        }

    }
}
