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
        Schema::create('fuel_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuel_stations');
    }
};
