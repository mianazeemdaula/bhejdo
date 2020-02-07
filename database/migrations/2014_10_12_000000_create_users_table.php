<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('mobile',11)->unique()->nullable();
            $table->string('password');
            $table->string('email')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('mobile_verified_at')->nullable();
            $table->string('cnic_front')->nullable();
            $table->string('cnic_back')->nullable();
            $table->string('cnic_verified_at')->nullable();
            $table->string('account_type')->default('consumer');
            $table->string('address')->default('Lahore');
            $table->string('city')->default('Lahore');
            $table->double('longitude')->default(0.0);
            $table->double('latitude')->default(0.0);
            $table->string('pushToken')->nullable();
            $table->integer('referred_by')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
