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
        Schema::create('fuel_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('station_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('current_qty', 15, 2)->default(0);
            $table->date('date')->default(now());
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending, approved, rejected');
            $table->timestamps();
            //
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('station_id')->references('id')->on('fuel_stations')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('fuel_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuel_requisitions');
    }
};
