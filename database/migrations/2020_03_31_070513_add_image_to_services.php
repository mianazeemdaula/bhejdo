<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('urdu_name',25)->nullable();
            $table->mediumText('description')->nullable();
            $table->integer('cross_price')->default(0);
            $table->integer('lifter_price')->default(0);
            $table->string('img_url')->nullable();
            $table->string('scale',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['urdu_name','img_url', 'scale', 'cross_price', 'description']);
        });
    }
}
