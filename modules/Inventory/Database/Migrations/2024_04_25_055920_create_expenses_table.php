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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fuel', 'maintenance', 'others'])->default('others')->comment('fuel, maintenance, others');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('trip_type_id')->nullable();
            $table->string('trip_number')->nullable();
            $table->string('odometer_millage')->nullable();
            $table->decimal('vehicle_rent', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->date('date');
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending, approved, rejected');
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
        Schema::dropIfExists('expenses');
    }
};
