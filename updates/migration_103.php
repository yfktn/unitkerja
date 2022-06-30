<?php namespace Yfktn\UnitKerja\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration103 extends Migration
{
    public function up()
    {
        Schema::table('yfktn_tulisan_tulis', function ($table) {
            $table->integer('unit_kerja_id')->unsigned()->index()->nullable();
        });
    }

    public function down()
    {
        Schema::table('yfktn_tulisan_tulis', function ($table) {
            $table->dropColumn('unit_kerja_id');
        });
    }
}