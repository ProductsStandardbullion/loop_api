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
        Schema::create('real_estate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->string('type',100)->nullable();
            $table->double('per_unit')->nullable();
            $table->date('start_date')->nullable();
            $table->string('payout_type',100)->nullable();
            $table->string('unit_type')->nullable();
            $table->string('investment_category',100)->nullable();
            $table->string('land_title',150)->nullable();
            $table->json('gallery')->nullable();
            $table->string('investment_id')->nullable();
            $table->string('video_link')->nullable();
            $table->integer('total_units_available')->default(0);
            $table->text('about')->nullable();
            $table->string('minimum_duration',100)->nullable();
            $table->string('maximum_duration',100)->nullable();
            $table->text('terms')->nullable();
            


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate');
    }
};
