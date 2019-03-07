<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditWooInfoSumProductStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('woo_infos', function (Blueprint $table) {
            //
            $table->integer('external')->after('status');
            $table->integer('grouped')->after('external');
            $table->integer('simple')->after('grouped');
            $table->integer('variable')->after('simple');
            $table->smallInteger('compare')->after('variable');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('woo_infos', function (Blueprint $table) {
            //
            $table->dropColumn('external');
            $table->dropColumn('grouped');
            $table->dropColumn('simple');
            $table->dropColumn('variable');
            $table->dropColumn('compare');
        });
    }
}
