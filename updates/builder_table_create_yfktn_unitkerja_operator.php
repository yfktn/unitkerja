<?php namespace Yfktn\UnitKerja\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnUnitkerjaOperator extends Migration
{
    public function up()
    {
        Schema::create('yfktn_unitkerja_operator', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('unit_kerja_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_unitkerja_operator');
    }
}