<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('over_time_status')->default(0);
            $table->boolean('salary_status')->default(0);
            $table->double('ot_payment', 8, 2)->nullable();
            $table->double('performance_bonus', 8, 2)->nullable();
            $table->double('penalty_amount', 8, 2)->nullable();
            $table->string('penalty_reason')->nullable();
            $table->date('penalty_date')->nullable();
            $table->date('insert_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_performances');
    }
};
