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
        Schema::create('ev_delivery_man_logs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // User ID
            $table->string('user_type'); // User type as string
            $table->timestamp('punched_in')->nullable(); // Punched in time, nullable
            $table->timestamp('punched_out')->nullable(); // Punched out time, nullable
            $table->string('status')->default('inactive'); // Status with default value 'inactive'
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ev_delivery_man_logs');
    }
};
