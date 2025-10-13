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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();  // Automatically creates an unsigned BIGINT column for the ID
            $table->string('name');  // Name of the zone
            $table->polygon('coordinates');  // For storing polygon coordinates (MySQL specific)
            $table->boolean('status')->default(true);  // Status flag, default to 1 (active)
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
        Schema::dropIfExists('zones');
    }
};
