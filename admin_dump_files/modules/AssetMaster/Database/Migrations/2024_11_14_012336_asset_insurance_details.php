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
        Schema::create('ev_vehicle_insurance', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('vehicle_reg_no')->nullable();
            $table->string('Insurance_Vendor_3rd_party')->nullable();
            $table->string('Policy_Number_3rd_party')->nullable();
            $table->date('Start_date_3rd_party')->nullable();
            $table->date('End_date_3rd_party')->nullable();
            $table->decimal('Declared_Value_3rd_party', 10, 2)->nullable();
            $table->string('Policy_Number_OD')->nullable();
            $table->date('Start_date_OD')->nullable();
            $table->date('End_date_OD')->nullable();
            $table->decimal('Declared_Value_OD', 10, 2)->nullable();
            $table->string('Insurance_Status_OD')->nullable();
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
        Schema::dropIfExists('ev_vehicle_insurance');
    }
};
