<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryManTable extends Migration
{
    public function up()
    {
        Schema::create('ev_tbl_delivery_men', function (Blueprint $table) {
            $table->id(); // auto-increment id
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->unsignedBigInteger('current_city_id')->nullable();
            $table->unsignedBigInteger('interested_city_id')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->integer('rider_type')->nullable();
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->timestamp('register_date_time')->nullable();
            $table->integer('rider_status')->default(0);
            $table->integer('approved_status')->default(0);
            $table->string('approver_role')->nullable();
            $table->string('approver_id')->nullable();
            $table->text('remarks')->nullable();
            $table->string('apply_job_source')->nullable();
            $table->string('referral')->nullable();
            $table->string('referal_person_name')->nullable();
            $table->string('referal_person_number', 20)->nullable();
            $table->string('job_agency')->nullable();
            $table->string('photo')->nullable();
            $table->string('aadhar_card_front')->nullable();
            $table->string('aadhar_card_back')->nullable();
            $table->string('aadhar_number', 20)->nullable();
            $table->string('pan_card_front')->nullable();
            $table->string('pan_card_back')->nullable();
            $table->string('pan_number', 15)->nullable();
            $table->string('driving_license_front')->nullable();
            $table->string('driving_license_back')->nullable();
            $table->string('bank_passbook')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_mobile_number', 15)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_mobile_number', 15)->nullable();
            $table->integer('marital_status')->default(0);
            $table->string('spouse_name')->nullable();
            $table->string('spouse_mobile_number', 15)->nullable();
            $table->string('emergency_contact_person_1_name')->nullable();
            $table->string('emergency_contact_person_1_mobile', 15)->nullable();
            $table->string('emergency_contact_person_2_name')->nullable();
            $table->string('emergency_contact_person_2_mobile', 15)->nullable();
            $table->string('blood_group', 3)->nullable();
            $table->text('fcm_token')->nullable();
            $table->text('remember_token')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('ev_tbl_delivery_men');
    }
}
