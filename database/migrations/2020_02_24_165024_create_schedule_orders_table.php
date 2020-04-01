<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consumer_id');
            $table->unsignedBigInteger('lifter_id');
            $table->unsignedBigInteger('service_id');
            $table->integer('qty');
            $table->double('price');
            $table->integer('charges');
            $table->dateTime('delivery_time');
            $table->string('address');
            $table->double('longitude')->default(0.0);
            $table->double('latitude')->default(0.0);
            $table->timestamps();
        });
    }

    /* 
     id            | bigint(20) unsigned | NO   | PRI | NULL    | auto_increment |
| consumer_id   | bigint(20) unsigned | NO   |     | NULL    |                |
| lifter_id     | bigint(20) unsigned | NO   |     | NULL    |                |
| service_id    | bigint(20) unsigned | NO   |     | NULL    |                |
| qty           | int(11)             | NO   |     | NULL    |                |
| price         | double              | NO   |     | NULL    |                |
| charges       | int(11)             | NO   |     | NULL    |                |
| shift         | tinyint(4)          | YES  |     | 1       |                |
| delivery_time | time                | YES  |     | NULL    |                |
| note          | varchar(192)        | YES  |     | NULL    |                |
| address       | varchar(255)        | NO   |     | NULL    |                |
| longitude     | double              | NO   |     | 0       |                |
| latitude      | double              | NO   |     | 0       |                |
| created_at    | timestamp           | YES  |     | NULL    |                |
| updated_at    | timestamp           | YES  |     | NULL    |                |
| schedule_type | int(11)             | NO   |     | 0       |                |
| days          | json                | YES  |     | NULL    |                |
| status        | tinyint(1)     
*/

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_orders');
    }
}
