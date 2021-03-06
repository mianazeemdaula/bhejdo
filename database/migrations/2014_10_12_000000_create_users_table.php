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
            $table->string('account_type')->default('consumer');
            $table->string('pushToken')->nullable();
            $table->string('referred_by',11)->nullable();
            $table->string('status',12)->default('unvarified');
            $table->string('city')->nullable()->default('Lahore');
            $table->string('address')->default('Lahore');
            $table->string('reffer_id',11)->nullable();
            $table->integer('pin')->default(0);
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
