<?php

namespace App\Http\Controllers\User\Investements\Real_estate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RealEstate;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RealEstateInvestMentController extends Controller
{


    public $resp = [];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investments = RealEstate::get(['id','title','per_unit','roi','	investment_id']);
        $this->resp['status'] = true;
        $this->resp['data'] = collect($investments);
        return response()->json([$this->resp], 200);
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
            'loop_id' => 'required|string',
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
        $data =  DB::table('investments')->insert([
            'loop_id' => $loop_id,
            'roi' => $request->roi,
            'principal' => $request->amount,
            'investment_id' => $request->investment_id,
            'porfolio_id' =>  strtolower(auth('sanctum')->user()->id . uniqid()) . Str::random(10)
        ]);
        $this->resp['status'] = true;
        $this->resp['data'] = $data;

        return response()->json($this->resp);
    }

    /**
     * Display the specified resource.
     */
    public function show($investment_id)
    {
        $investment = RealEstate::where('investment_id', $investment_id)->first();
        $this->resp['status'] = true;
        $this->resp['data'] = collect($investment);
        return response()->json([$this->resp], 200);
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


}
