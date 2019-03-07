<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewlinks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->text('link');
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
        Schema::dropIfExists('reviewlinks');
    }
}
