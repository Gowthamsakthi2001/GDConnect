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
        Schema::create('ev_tbl_lead_source', function (Blueprint $table) {
            $table->id(); // Equivalent to int(11) and auto-increment
            $table->string('source_name', 100)->nullable(); // varchar(100) DEFAULT NULL
            $table->integer('status')->default(1); // int(11) NOT NULL DEFAULT '1'
            $table->timestamp('created_at')->useCurrent(); // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // timestamp with current time on update
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ev_tbl_lead_source');
    }
};
