<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ev_manufacturer_master', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('manufacturer_name')->nullable();
            $table->string('Address_line_1')->nullable();
            $table->string('Address_line_2')->nullable();
            $table->string('Address_line_3')->nullable();
            $table->string('Country')->nullable();
            $table->string('State')->nullable();
            $table->string('Phone')->nullable();
            $table->string('Contact_Name')->nullable();
            $table->string('Status')->nullable();
            $table->string('Web_site_URL')->nullable();
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
        Schema::dropIfExists('ev_manufacturer_master');
    }
};
