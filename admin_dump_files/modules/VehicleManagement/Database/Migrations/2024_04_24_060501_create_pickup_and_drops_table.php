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
        Schema::create('pickup_and_drops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->boolean('request_type')->default(0)->comment('0=Regular, 1=Specific day');
            $table->enum('type', ['Pickup', 'Drop', 'PickDrop'])->nullable();
            $table->date('effective_date')->nullable();
            $table->boolean('status')->default(0)->comment('0=Pending, 1=Release');
            $table->enum('is_approved', ['Pending', 'Rejected', 'Approved'])->default('Pending');
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
        Schema::dropIfExists('pickup_and_drops');
    }
};
