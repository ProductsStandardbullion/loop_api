<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('loop_id', 100);
            $table->double('amount');
            $table->string('type',20);
            $table->string('transaction_id',50);
            $table->string('portfolio_id',50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_returns');
    }
};
