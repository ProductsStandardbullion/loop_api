<?php

namespace App\Http\Controllers\User\Bank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankDetailsController extends Controller
{
    

    public $resp = [];


    public function index(){
        $data =    DB::table('bank_details')->where('loop_id', auth('sanctum')->user()->loop_id)->first();
        $this->resp['status'] = true;
        $this->resp['data'] = $data;
        return response()->json($this->resp);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'bank' => 'required',
            'account_number' => 'required',
            'account_name' => 'required'
        ]);
        DB::table('bank_details')->where('loop_id', auth('sanctum')->user()->loop_id)->delete();
        DB::table('bank_details')->insert([
            'bank' => ucwords($request->bank),
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'loop_id' => session('user')->get('loop_id')

        ]);
        $this->resp['status'] = true;
        $this->resp['message'] = 'Your withdrawal details have been saved.';
        return response()->json($this->resp,201);
    }







}
