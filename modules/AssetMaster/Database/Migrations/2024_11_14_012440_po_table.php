<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ev_po_table', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->string('AMS_Location')->nullable();
            $table->string('PO_Number')->nullable();
            $table->string('Supplier_Name')->nullable();
            $table->text('Description')->nullable();
            $table->string('Manufacturer')->nullable();
            $table->date('PO_Date')->nullable();
            $table->decimal('Other_Amount', 10, 2)->nullable();
            $table->decimal('Tax_Amount', 10, 2)->nullable();
            $table->date('Delivery_Date')->nullable();
            $table->integer('Quantity')->nullable();
            $table->string('Status')->nullable();
            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ev_po_table');
    }
};
