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
        Schema::create('ev_modal_master_charger', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('name')->nullable();
            $table->string('manufacturer_name')->nullable();
            $table->decimal('nominal_c_rating', 8, 2)->nullable();
            $table->string('charging_mode')->nullable();
            $table->decimal('output_voltage', 8, 2)->nullable();
            $table->decimal('output_current', 8, 2)->nullable();
            $table->decimal('input_voltage', 8, 2)->nullable();
            $table->decimal('input_current', 8, 2)->nullable();
            $table->integer('connector_rating')->nullable();
            $table->string('status')->nullable();
            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ev_modal_master_charger');
    }
};
