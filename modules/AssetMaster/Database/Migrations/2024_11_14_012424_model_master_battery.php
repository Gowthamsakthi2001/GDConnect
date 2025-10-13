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
        Schema::create('ev_modal_master_battery', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('name')->nullable();
            $table->string('manufacturer_name')->nullable();
            $table->decimal('current_rating_Ah', 8, 2)->nullable();
            $table->string('type')->nullable();
            $table->string('cell_chemistry')->nullable();
            $table->decimal('nominal_voltage', 8, 2)->nullable();
            $table->decimal('max_discharge_rate_c', 8, 2)->nullable();
            $table->decimal('max_voltage', 8, 2)->nullable();
            $table->decimal('min_voltage', 8, 2)->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->string('connector_type')->nullable();
            $table->boolean('telematics_enabled')->nullable();
            $table->string('type_of_telematics')->nullable();
            $table->boolean('smart_bms_available')->nullable();
            $table->json('smart_bms_features')->nullable();
            $table->string('cell_structure')->nullable();
            $table->string('cell_model')->nullable();
            $table->string('ip_rating')->nullable();
            $table->integer('dod_percentage')->nullable();
            $table->integer('connector_rating')->nullable();
            $table->integer('warranty_expiry_cycles')->nullable();
            $table->integer('warranty_expiry_duration')->nullable();
            $table->string('warranty_expiry_param_priority')->nullable();
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
        Schema::dropIfExists('ev_modal_master_battery');
    }
};
