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
        // 'route_name
        // 'destination
        // 'start_p
        // 'descrip

        Schema::create('vehicle_route_details', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('starting_point')->nullable();
            $table->string('destination_point')->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('create_pick_drop_point')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('vehicle_route_details');
    }
};
