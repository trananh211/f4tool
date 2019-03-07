<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewAmzReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amz_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('amz_collection_id');
            $table->integer('category_id');
            $table->smallInteger('star');
            $table->mediumText('title');
            $table->text('content');
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
        Schema::dropIfExists('amz_reviews');
    }
}
