<?php

namespace App\Http\Controllers\User\Withdrawal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public $resp = [];


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'portfolio_id' => 'required'
        ]);

        if ($validator->fails()) {

            $this->resp['status'] = false;
            $this->resp['error'] = $validator->errors();
            return response()->json($this->resp, 400);
        } else {

            $principal = DB::table('portfolio')->where('loop_id', auth('sanctum')->user()->loop_id)->where('portfolio_id', $request->portfolio_id)->first('principal')->principal;
            $returns = DB::table('daily_returns')->where('loop_id', auth('sanctum')->user()->loop_id)->where('portfolio_id', $request->portfolio_id)->sum('amount');

            $amount = $returns + $principal;




            DB::beginTransaction();

            $withdraw = new Withdrawal();
            $withdraw->portfolio_id = $request->portfolio_id;
            $withdraw->loop_id = auth('sanctum')->user()->loop_id;
            $withdraw->amount = $amount;
            $withdraw->status = false;
            $withdraw->created_at = now();
            $withdraw->updated_at = now();
            $withdraw->save();


            //debit investment wallet
            DB::table('users')->where('loop_id', auth('sanctum')->user()->loop_id)->decrement('investment', $returns);

            //update dail return table
            DB::table('daily_returns')->where('loop_id', auth('sanctum')->user()->loop_id)->where('portfolio_id', $request->portfolio_id)->update(['status' => 0, 'updated_at' => now()]);
            DB::table('portfolio')->where('loop_id', auth('sanctum')->user()->loop_id)->where('portfolio_id', $request->portfolio_id)->update(['status' => 0, 'updated_at' => now()]);

            DB::commit();

            //Send email to support
            $mailData = [
                'title' => auth('sanctum')->user()->first_name . ', has requested a withdrawal',
                'message' => '<p>Hi support, ' . auth('sanctum')->user()->first_name . ' has requested a withdrawal. </p>
            <div> <h3>Transaction details</h3>
            <p>Customer: ' . auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name . '</p>
            <p>Amount: ₦' . number_format($request->amount) . '</p>
            
            </div>
            '
            ];

            //Mail::to($email_address)->send(new Email($mailData));
            Mail::to('deposits@loopoptions.com')->send(new \App\Mail\Support($mailData));

            $this->resp['status'] = true;
            $this->resp['message'] = 'Your withdrawal request has been submitted.';
            return response()->json($this->resp);
        }
    }
}
