<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('consumer_id');
            $table->integer('qty');
            $table->double('price');
            $table->dateTime('delivery_time');
            $table->string('address');
            $table->double('longitude')->default(0.0);
            $table->double('latitude')->default(0.0);
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
        Schema::dropIfExists('open_orders');
    }
}
