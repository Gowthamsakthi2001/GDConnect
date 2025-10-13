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
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('maintenance_type_id')->nullable();
            $table->string('title')->nullable();
            $table->date('date')->default(now());
            $table->text('remarks')->nullable();
            $table->string('charge_bear_by')->nullable();
            $table->decimal('charge', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('type', ['maintenance', 'general'])->default('general')->comment('maintenance, general');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low')->comment('low, medium, high');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending, approved, rejected');
            $table->timestamps();
            // foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('maintenance_type_id')->references('id')->on('vehicle_maintenance_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_maintenances');
    }
};
