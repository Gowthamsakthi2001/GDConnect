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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver_code')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('license_type_id')->constrained('license_types')->nullable();
            $table->string('license_num')->nullable();
            $table->date('license_issue_date')->nullable();
            $table->string('nid')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('authorization_code')->nullable();
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('working_time_slot')->nullable();
            $table->boolean('leave_status')->default(0);
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('drivers');
    }
};
