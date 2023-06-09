<?php

namespace App\Http\Controllers\User\Portfolio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Validator;
use Illuminate\Support\Facades\Mail;

class PortfolioController extends Controller
{
    public $resp = [];


    /**
     * Display a listing of the resource.
     */
    public function portfolio()
    {
        $loop_id = auth('sanctum')->user()->loop_id;

     
        $portfolio = Portfolio::where('loop_id', $loop_id)->where('status',1)->get();
        if(empty($portfolio)){
            $this->resp['status'] = false;
            $this->resp['error'] = 'You have not made any investment.';
            return response()->json($this->resp, 200);

        }else{
            $this->resp['status'] = true;
            $this->resp['data'] = collect($portfolio);
            return response()->json($this->resp, 200);
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
        $validator = Validator::make($request->all(), [
            'roi' => 'required|numeric',
            'units' => 'required|numeric',
            'principal' => 'required',
            'investment_id' => 'required'
        ]);

        if ($validator->fails()) {
            $this->resp['status'] = false;
            $this->resp['message'] = 'Something went wrong.';
            $this->resp['error'] = $validator->errors();
            return response()->json($this->resp, 401);

        }else{
    
            $portfolio = new Portfolio();
            $portfolio->roi = $request->roi;
            $portfolio->principal = $request->total;
            $portfolio->investment_id = $request->investment_id;
            $portfolio->created_at = now();
            $portfolio->updated_at = now();
            $portfolio->save();

            $this->resp['status'] = true;
            $this->resp['data'] = $portfolio;
            return response()->json($this->resp,201);

        }
    }

  

   
}
