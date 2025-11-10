<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('room')->default(0);
            $table->integer('self')->default(0);
            $table->integer('drawer')->default(0);
            $table->integer('capacity')->default(0);
            $table->integer('dimension')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_locations');
    }
};
