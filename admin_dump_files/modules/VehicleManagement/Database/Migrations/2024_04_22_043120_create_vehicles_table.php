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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('registration_date')->nullable();
            $table->string('license_plate')->nullable();
            $table->string('alert_cell_no')->nullable();
            $table->string('alert_email')->nullable();
            $table->unsignedBigInteger('ownership_id')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->unsignedBigInteger('vehicle_division_id')->nullable();
            $table->unsignedBigInteger('rta_circle_office_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->integer('seat_capacity')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
};
