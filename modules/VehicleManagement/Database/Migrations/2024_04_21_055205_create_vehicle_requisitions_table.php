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
        Schema::create('vehicle_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->string('where_from')->nullable();
            $table->string('where_to')->nullable();
            $table->string('pickup')->nullable();
            $table->date('requisition_date')->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->string('tolerance')->nullable();
            $table->integer('number_of_passenger')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('purpose')->nullable();
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending, approved, rejected');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_requisitions');
    }
};
