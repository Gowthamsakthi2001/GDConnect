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
        Schema::create('ev_Asset_Master_Vehicle', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('Reg_No')->nullable();
            $table->string('Model')->nullable();
            $table->string('Manufacturer')->nullable();
            $table->string('Original_Motor_ID')->nullable();
            $table->string('Chassis_Serial_No')->nullable();
            $table->string('Purchase_order_ID')->nullable();
            $table->integer('Warranty_Kilometers')->nullable();
            $table->string('Hub')->nullable();
            $table->string('Client')->nullable();
            $table->string('Colour')->nullable();
            $table->date('Asset_In_Use_Date')->nullable();
            $table->string('Deployed_To')->nullable();
            $table->string('Emp_ID')->nullable();
            $table->date('Procurement_Lease_Start_Date')->nullable();
            $table->date('Lease_Rental_End_Date')->nullable();
            $table->text('PO_Description')->nullable();
            $table->string('Registration_Type')->nullable();
            $table->string('Ownership_Type')->nullable();
            $table->decimal('Lease_Value', 15, 2)->nullable();
            $table->string('AMS_Location')->nullable();
            $table->string('Parking_Location')->nullable();
            $table->string('Asset_Status')->nullable();
            $table->string('Sub_Status')->nullable();
            $table->boolean('is_swappable')->nullable();
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
        Schema::dropIfExists('ev_Asset_Master_Vehicle');
    }
};
