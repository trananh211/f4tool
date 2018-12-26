<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewWoocommerceInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('woo_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('woo_name', 255);
            $table->string('woo_link', 255)->unique();
            $table->text('consumer_key');
            $table->text('consumer_secret');
            $table->smallInteger('status');
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
        Schema::dropIfExists('woo_infos');
    }
}
