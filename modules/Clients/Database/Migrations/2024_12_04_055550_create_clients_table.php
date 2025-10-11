<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('ev_tbl_clients', function (Blueprint $table) {
            $table->id(); // Auto incrementing ID column
            $table->string('client_name'); // Client Name
            $table->string('client_zone'); // Client Zone
            $table->string('client_location'); // Client Location
            $table->string('hub_name'); // Hub Name
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('ev_tbl_clients');
    }
}
