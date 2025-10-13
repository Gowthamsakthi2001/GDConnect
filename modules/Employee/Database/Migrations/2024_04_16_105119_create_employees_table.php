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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->nullable();
            $table->string('name');
            $table->string('payroll_type')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('nid')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('phone2')->nullable();
            $table->date('join_date')->nullable();
            $table->string('blood_group')->nullable();
            $table->date('dob')->nullable();
            $table->string('working_slot_from')->nullable();
            $table->string('working_slot_to')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('present_contact')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_contact')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('present_city')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->string('reference_name')->nullable();
            $table->string('reference_mobile')->nullable();
            $table->string('reference_email')->nullable();
            $table->string('reference_address')->nullable();
            $table->string('avatar_path')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
