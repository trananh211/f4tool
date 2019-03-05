<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewAmzCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amz_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
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
        Schema::dropIfExists('amz_collections');
    }
}
