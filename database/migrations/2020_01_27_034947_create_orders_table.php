<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lifter_id');
            $table->integer('consumer_id');
            $table->integer('service_id');
            $table->integer('qty');
            $table->double('price');
            $table->dateTime('delivery_time');
            $table->string('note')->nullable();
            $table->string('address');
            $table->double('longitude')->default(0.0);
            $table->double('latitude')->default(0.0);
            $table->dateTime('created_time')->nullable();
            $table->dateTime('accepted_time')->nullable();
            $table->dateTime('shipped_time')->nullable();
            $table->dateTime('delivered_time')->nullable();
            $table->dateTime('confirmed_time')->nullable();
            $table->dateTime('cancel_time')->nullable();
            $table->string('cancel_desc')->nullable();
            $table->string('status',15)->default('created');
            $table->integer('type')->default(1); // 1- Open, 2-Schedule, 3-Sample
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
        Schema::dropIfExists('orders');
    }
}
