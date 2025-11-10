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
        Schema::create('vehicle_refuelings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('fuel_type_id')->nullable();
            $table->unsignedBigInteger('fuel_station_id')->nullable();
            $table->timestamp('refueled_at')->nullable()->default(now());
            $table->string('place')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->decimal('km_per_unit', 15, 2)->default(0);
            $table->decimal('last_reading', 15, 2)->default(0);
            $table->decimal('last_unit', 15, 2)->default(0);
            $table->decimal('refuel_limit', 15, 2)->nullable();
            $table->decimal('max_unit', 15, 2)->default(0);
            $table->decimal('unit_taken', 15, 2)->default(0);
            $table->string('odometer_day_end')->nullable();
            $table->string('odometer_refuel_time')->nullable();
            $table->decimal('consumption_percent')->nullable();
            $table->string('slip_path')->nullable();
            $table->boolean('strict_policy')->default(0);
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
        Schema::dropIfExists('vehicle_refuelings');
    }
};
