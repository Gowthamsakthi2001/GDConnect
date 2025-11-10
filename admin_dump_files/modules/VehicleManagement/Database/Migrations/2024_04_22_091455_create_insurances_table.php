<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('policy_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->double('charge_payable')->nullable();
            $table->double('deductible')->nullable();
            $table->date('recurring_date')->nullable();
            $table->unsignedBigInteger('recurring_period_id')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('add_reminder')->default(0);
            $table->longText('remarks')->nullable();
            $table->string('policy_document_path')->nullable();
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
        Schema::dropIfExists('insurances');
    }
};
