<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public $resp = [];




    public function send_verification_email($email_address, $loop_id, $first_name)
    {

        // DB::table('verification_code')->where('loop_id', $loop_id)->delete();
        // $code = strtoupper(Str::random(6));
        // $current_time = time();
        // $future_time = $current_time + (15 * 60);
        // $future_time_formatted = date('Y-m-d H:i:s', $future_time);
        // DB::table('verification_code')->insert([
        //     'loop_id' => $loop_id,
        //     'expires_at' => $future_time_formatted,
        //     'code' => $code,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        $mailData = [
            'title' => $first_name . ', here is your ' . env('app.name') . ' verification link',
            'code' => route('verify.account',['id' => $loop_id])
        ];

        //Mail::to($email_address)->send(new Email($mailData));
        Mail::to($email_address)->send(new \App\Mail\Email($mailData));
    }
    public function create_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            $this->resp['status'] = false;
            $this->resp['message'] = 'Something went wrong.';
            $this->resp['error'] = $validator->errors();
            return response()->json([$this->resp], 400);
        }
        $loop_id = Str::uuid();

        $df = abs(strlen($request->referred_by) - 8);

        $ref = substr($request->referred_by, 0, $df);
        if (strlen($ref) > 0) {

            //Create new user
            $user = new User();
            $user->first_name = trim(ucfirst($request->first_name));
            $user->last_name = trim(ucfirst($request->last_name));
            $user->email = trim(strtolower($request->email));
            $user->phone = trim($request->phone);
            $user->referral_code = null;
            $user->referred_by = $ref ?? null;
            $user->password = Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]);
            $user->loop_id = $loop_id;
            $user->save();
        } else {
            //Create new user
            $user = new User();
            $user->first_name = trim(ucfirst($request->first_name));
            $user->last_name = trim(ucfirst($request->last_name));
            $user->email = trim(strtolower($request->email));
            $user->phone = trim($request->phone);
            $user->referral_code = null;
            $user->password = Hash::make($request->password, ['memory' => 1024, 'time' => 2, 'threads' => 2,]);
            $user->loop_id = $loop_id;
            $user->save();
        }



        $data['loop_id'] = $loop_id;
        $data['user'] = $request->all();
        //Trigger email
        //$this->send_verification_email($request->email, $loop_id, trim(ucfirst($request->first_name)));
        $this->resp['status'] = true;
        $this->resp['data'] = $data;
        $this->resp['message'] = 'Your account is almost ready. Use the code that was sent to your email address to verify your account.';
        return response()->json($this->resp, 201);
    }

    public function login(Request $request)
    {
        $field = $this->validate($request, [

            'email' => 'required|email',
            'password' => 'required'
        ]);
        //check user email
        $user = User::where('email', $field['email'])->first();


        //check if account exists
        if (empty($user)) {
            $this->resp['status'] = false;
            $this->resp['error'] = 'This account does not exists. Create an account to get started';
            return response()->json($this->resp);
        }

        if (!$user || !Hash::check($field['password'], $user->password)) {
            $this->resp['status'] = false;
            $this->resp['error'] = 'Incorrect login details. Please try again.';
            return response()->json($this->resp);
        }



        //delete old access token
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();




        $token = $user->createToken('Auth_token')->plainTextToken;
        $response = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
        $this->resp['status'] = true;
        $this->resp['data'] = $response;
        return response()->json($this->resp);
    }




    public function userObject()
    {
        $data = User::find(auth('sanctum')->user()->id);
        $this->resp['status'] = true;
        $this->resp['data'] = collect($data);
        return response()->json($this->resp);
    }




    public function verify($id){
        DB::table('users')->where('loop_id', $id)->update(['email_verified_at' => now(), 'updated_at','verified' => 1]);
        return redirect('https://www.app.loopoptions.com/')->with('success', 'Congratulations, your account has been verified.');

    }
}
