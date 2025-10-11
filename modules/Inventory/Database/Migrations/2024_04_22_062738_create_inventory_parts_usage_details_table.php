<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_parts_usage_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parts_usage_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('parts_id')->nullable();
            $table->integer('qty')->default(0);
            $table->timestamps();

            $table->foreign('parts_usage_id')->references('id')->on('inventory_parts_usages')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('inventory_categories')->onDelete('set null');
            $table->foreign('parts_id')->references('id')->on('inventory_parts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_parts_usage_details');
    }
};
