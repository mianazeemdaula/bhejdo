<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('sotre_id')->nullable();
            $table->unsignedBigInteger('lifter_id')->nullable();
            $table->unsignedBigInteger('payment_id')->default(1);
            $table->integer('address_id');
            $table->integer('charges')->default(0);
            $table->time('delivery_time');
            $table->string('note')->nullable();
            $table->integer('type')->default(1);
            $table->integer('consumer_bonus');
            $table->integer('store_amount');
            $table->integer('lifter_amount');
            $table->integer('payable_amount');
            $table->string('status',15);
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
        Schema::dropIfExists('cart_orders');
    }
}
