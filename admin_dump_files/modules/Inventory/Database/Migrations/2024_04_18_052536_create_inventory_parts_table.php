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
        Schema::create('inventory_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->onDelete('set null');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->integer('qty')->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('inventory_parts');
    }
};
