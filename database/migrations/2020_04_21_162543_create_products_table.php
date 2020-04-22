<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('city_id');
            $table->string('name');
            $table->string('urdu_name')->nullable();
            $table->double('min_qty')->default(2);
            $table->double('max_qty')->default(10);
            $table->double('min_qty_charges')->default(10);
            $table->double('contract_price');
            $table->double('sale_price')->nullable();
            $table->double('markeet_price');
            $table->double('store_commission')->default(8);
            $table->double('lifter_commission')->default(5);
            $table->double('weight')->default(100); // always in grams
            $table->string('thumbnil_url')->nullable();
            $table->string('img_url')->nullable();
            $table->string('unit')->default('ltr');
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
        Schema::dropIfExists('products');
    }
}
