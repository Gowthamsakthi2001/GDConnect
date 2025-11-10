<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ev_rider_types', function (Blueprint $table) {
            $table->id(); // Primary key with auto-incrementing ID
            $table->string('type')->nullable(); // Type column as a nullable string
            $table->string('status')->nullable(); // Status column as a nullable string
            $table->timestamp('created_at')->useCurrent(); // Created at timestamp with default current time
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Updated at timestamp with default current time and updates on modification
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ev_rider_types');
    }
};
