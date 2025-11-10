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
        Schema::create('ev_tbl_otp_verification', function (Blueprint $table) {
            $table->id();  // Primary key ID
            $table->string('otp');  // OTP as string
            $table->unsignedBigInteger('type_id');  // Foreign key or related type ID
            $table->string('type');  // Type as string
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ev_tbl_otp_verification');
    }
};
