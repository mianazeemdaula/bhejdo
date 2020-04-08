<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('partner_id')->unqiue();
            $table->string('name');
            $table->point('location')->nullable();
            $table->boolean('onwork')->default(0);
            $table->json('services');
            $table->string('pushToken')->nullable();
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
        Schema::dropIfExists('partner_locations');
    }
}
