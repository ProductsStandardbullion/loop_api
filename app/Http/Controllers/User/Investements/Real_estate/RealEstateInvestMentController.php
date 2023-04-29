<?php
namespace App\Http\Controllers\User\Investements\Real_estate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RealEstate;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Log;

class RealEstateInvestMentController extends Controller
{


    public $resp = [];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investments = RealEstate::get();
        $this->resp['status'] = true;
        $this->resp['data'] = collect($investments);
        return response()->json($this->resp, 200);
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

        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|string',
            'roi' => 'required',
            'principal' => 'required'
        ]);
        if ($validator->fails()) {

            $this->resp['status'] = false;
            $this->resp['message'] = 'Something went wrong.';
            $this->resp['error'] = $validator->errors();
            return response()->json([$this->resp], 400);
        }
        $loop_id = auth('sanctum')->user()->loop_id;

        //check balance


        if (auth('sanctum')->user()->wallet < $request->principal) {
            $this->resp['status'] = false;
            $this->resp['error'] = 'Insufficient balance.';
            return response()->json($this->resp, 400);
        } else {

            try {
                //code...



                $investment = DB::table('real_estate')->where('investment_id', $request->investment_id)->first();

                $returns = round((($request->roi / $investment->maximum_duration) / 30) / 100, 4);
                $portfolio_id =  strtolower(uniqid() . auth('sanctum')->user()->id . Str::random(10));

                DB::table('portfolio')->insert([
                    'loop_id' => $loop_id,
                    'roi' => $request->roi,
                    'principal' => $request->principal,
                    'investment_id' => $request->investment_id,
                    'portfolio_id' => $portfolio_id,
                    'returns' => $returns,
                    'units' => $request->units,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);




                $this->resp['status'] = true;
                $this->resp['data'] = $request->all();
                $email_address = DB::table('users')->where('loop_id', $loop_id)->first()->email;

                DB::table('users')->where('id', auth('sanctum')->user()->id)->decrement('wallet', $request->principal);
                DB::table('real_estate')->where('investment_id', $request->investment_id)->decrement('total_units_available', $request->units);
                DB::table('real_estate')->where('investment_id', $request->investment_id)->increment('participants', $request->units);
                $start =  date('Y-m-d', strtotime($investment->start_date . ' +2 day'));
                $mailData = [
                    'title' => 'You have invested in ' . $investment->investment->title,
                    'project' => $investment->title,
                    'start' => $start,
                    'roi' => $investment->roi .'%' ,
                    'duraration' => $investment->maximum_duration .' months',
                    'name' => auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name,
                    'last_name' => auth('sanctum')->user()->last_name,
                    'expected_return' =>  '₦' . number_format(($request->principal * ($investment->roi / 100)) + $request->principal),
                    'amount' => '₦' . number_format($request->principal),
                    'slots' => $request->units,
                    'investment_id' => $portfolio_id,
                    'end' =>  date('Y-m-d', strtotime($investment->start_date  . ' +' . $investment->maximum_duration . ' months'))

                ];
                Mail::to($email_address)->send(new \App\Mail\Buy($mailData));
            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }






            return response()->json($this->resp);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $investment = RealEstate::where('investment_id', $id)->first();
        if (empty($investment)) {
            $this->resp['status'] = false;
            $this->resp['message'] = 'Item not found';
            return response()->json($this->resp, 404);
        } else {
            $this->resp['status'] = true;
            $this->resp['data'] = collect($investment);
            return response()->json($this->resp, 200);
        }
    }
}
