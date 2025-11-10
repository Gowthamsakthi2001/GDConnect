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
        Schema::create('ev_asset_master_battery', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('AMS_Location')->nullable();
            $table->string('PO_ID')->nullable();
            $table->string('Invoice_Number')->nullable();
            $table->string('Battery_Model')->nullable();
            $table->string('Serial_Number')->nullable();
            $table->string('Engraved_Serial_Num')->nullable();
            $table->string('Sub_status')->nullable();
            $table->date('In_use_Date')->nullable();
            $table->string('Assigned_To')->nullable();
            $table->string('Status')->nullable();
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
        Schema::dropIfExists('ev_asset_master_battery');
    }
};
