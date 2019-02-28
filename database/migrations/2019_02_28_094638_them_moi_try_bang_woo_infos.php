<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThemMoiTryBangWooInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('woo_infos', function (Blueprint $table) {
            $table->smallInteger('try')->default(1)->after('compare');
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
            $table->dropColumn('try');
        });
    }
}
