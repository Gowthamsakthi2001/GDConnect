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
        Schema::create('ev_tbl_leads', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('telecaller_status')->nullable();
            $table->integer('source')->nullable();
            $table->integer('assigned')->nullable();
            $table->string('f_name')->nullable();
            $table->string('l_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('current_city')->nullable();
            $table->string('intrested_city')->nullable();
            $table->integer('vehicle_type')->nullable();
            $table->integer('lead_sources')->nullable();
            $table->date('register_date')->nullable();
            $table->string('active_status')->nullable();
            $table->string('task')->nullable();
            $table->text('description')->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ev_tbl_leads');
    }
};
