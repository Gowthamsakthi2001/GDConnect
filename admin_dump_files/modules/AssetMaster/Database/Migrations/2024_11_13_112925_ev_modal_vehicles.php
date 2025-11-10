<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ev_modal_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('manufacturer_name');
            $table->integer('load_capacity_kg')->nullable();
            $table->integer('rated_voltage')->nullable();
            $table->integer('rated_Ah')->nullable();
            $table->integer('max_speed_km_h')->nullable();
            $table->string('tyre_type')->nullable();
            $table->string('front_tyre_dimensions')->nullable();
            $table->string('rear_tyre_dimensions')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->integer('range_km_noload')->nullable();
            $table->integer('range_km_fullload')->nullable();
            $table->string('vehicle_mode')->nullable();
            $table->string('motor_type')->nullable();
            $table->integer('motor_max_rpm')->nullable();
            $table->integer('peak_power_watt')->nullable();
            $table->integer('rated_power_watt')->nullable();
            $table->boolean('motor_can_enabled')->default(false);
            $table->integer('peak_torque_nm')->nullable();
            $table->integer('continuous_torque_nm')->nullable();
            $table->string('front_suspension_type')->nullable();
            $table->string('rear_suspension_type')->nullable();
            $table->integer('ground_clearance_mm')->nullable();
            $table->string('motor_ip_rating')->nullable();
            $table->string('throttle_type')->nullable();
            $table->integer('peak_curr_cntrlr')->nullable();
            $table->boolean('cntrlr_can_enabled')->default(false);
            $table->decimal('acceleration_0to40_sec', 5, 2)->nullable(); // Assuming it's a decimal with 2 precision points
            $table->string('head_light_type')->nullable();
            $table->boolean('vehicle_reverse_mode')->default(false);
            $table->boolean('inbuilt_iot')->default(false);
            $table->string('status')->default('active');
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
        Schema::dropIfExists('ev_modal_vehicles');
    }
};
