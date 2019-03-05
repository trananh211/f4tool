<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AmzProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amz_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amz_collection_id');
            $table->integer('category_id');
            $table->string('product_id',255)->comment('product id of amazon');
            $table->string('title',255);
            $table->text('link_origin');
            $table->text('link_page')->comment('this link is pagination of product');
            $table->smallInteger('status')->default(0)
                ->comment('0: not-check,1:checked');
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
        Schema::dropIfExists('amz_products');
    }
}
