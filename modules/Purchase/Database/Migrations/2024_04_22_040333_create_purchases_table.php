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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->date('date')->default(now());
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending, approved, rejected');
            $table->string('req_img_path')->nullable();
            $table->string('order_path')->nullable();
            $table->timestamps();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};
