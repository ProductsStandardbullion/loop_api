<?php

namespace App\Http\Controllers\User\Deposit\Direct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class DirectDepositController extends Controller
{
    public $resp = [];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $key = rand(8, 16);
        //Create narration
        $narration = strtolower(auth('sanctum')->user()->first_name . Str::random($key));

        $this->resp['status'] = true;
        $this->resp['data'] = $narration;
        return response()->json($this->resp);
    }

    public function history()
    {
        $loop_id = auth('sanctum')->user()->loop_id;
        $history = DB::table('direct_deposit')->where('loop', $loop_id)->get();
        if (!empty($history)) {
            $this->resp['status'] = true;
            $this->resp['data'] = collect($history);
            return response()->json($this->resp);
        } else {
            $this->resp['status'] = false;
            $this->resp['data'] = null;
            return response()->json($this->resp);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'amount' => 'required',
            'narration' => 'required',
            'fee' => 'required'
        ]);

        $amount = $data['amount'] - $data['fee'];
        DB::table('direct_deposit')->insert([
            'loop_id' => auth('sanctum')->user()->loop_id,
            'narration' => $data['narration'],
            'amount' => $amount,
            'created_at' => now(),
            'updated_at' => now()

        ]);

        //Send email to support
        $mailData = [
            'title' => auth('sanctum')->user()->first_name . ', has requested to deposit',
            'message' => '<p>Hi support, ' . auth('sanctum')->user()->first_name . ' has requested to make deposit to his wallet. Kindly look out for the alert. </p>
            <div> <h3>Transaction details</h3>
            <p>Customer: ' . auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name . '</p>
            <p>Amount: ₦' . number_format($request->amount) . '</p>
            <p>Narration:' . $request->narration . '</p>

            <p>Current balance:' . number_format(auth('sanctum')->user()->wallet) . '</p>
            
            </div>
            '
        ];

        //Mail::to($email_address)->send(new Email($mailData));
        Mail::to('deposits@loopoptions.com')->send(new \App\Mail\Support($mailData));

        $this->resp['status'] = true;
        $this->resp['message'] = 'Your deposit request has been submitted. Your wallet shall be credited when your deposit is confirmed.';

        return response()->json($this->resp);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
