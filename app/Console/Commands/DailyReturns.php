<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DailyReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-returns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily returns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $counter = 0;
        $total_sum = 0;
        $data = DB::table('portfolio')->where('status',1)->get(['loop_id','principal','returns','portfolio_id']);
        DB::beginTransaction();
        foreach ($data as $key => $value) {
            $daily_returns =  doubleval($value->returns * $value->principal);
            $total_sum = $total_sum + $daily_returns;
            //Log::info($daily_returns);
            DB::table('users')->where('loop_id',$value->loop_id)->increment('investment', $daily_returns);
            DB::table('daily_returns')->insert([
                'loop_id' => $value->loop_id,
                'amount' => $daily_returns,
                'type' => 'Credit',
                'portfolio_id' => $value->portfolio_id,
                'transaction_id' => strtolower(uniqid()),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $counter = $counter + 1;
            
        }

        DB::commit();

        $mailData = [
            'title' => number_format($counter) . ' investment wallets have been credited',
            'message' => '<p>Good day chief, this message to inform you that '. number_format($counter) .' investment wallets have been created with a total sum of ₦'. number_format($total_sum) .'</p>
            
            '

        ];
            //Mail::to($email_address)->send(new Email($mailData));
            Mail::to('deposits@loopoptions.com')->send(new \App\Mail\Support($mailData));
    }
}
