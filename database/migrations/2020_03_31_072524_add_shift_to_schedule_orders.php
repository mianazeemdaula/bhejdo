<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftToScheduleOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_orders', function (Blueprint $table) {
            $table->integer('shift')->default(0);
            $table->integer('note')->default(0);
            $table->integer('schedule_type')->default(0); // 0 - Daily, 2- Week Days , 3-monthly
            $table->json('days')->nullable();
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_orders', function (Blueprint $table) {
            $table->dropColumn(['shift','note','schedule_type','days','status']);
        });
    }
}
