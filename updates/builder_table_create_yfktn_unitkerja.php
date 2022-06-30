<?php namespace Yfktn\UnitKerja\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnUnitkerja extends Migration
{
    public function up()
    {
        Schema::create('yfktn_unitkerja_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('nama', 2024);
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->unsigned()->index()->nullable();
            $table->integer('nest_right')->unsigned()->index()->nullable();
            $table->integer('nest_depth')->unsigned()->index()->nullable();
            $table->text('keterangan')->nullable();
            $table->string('slug', 1024)->nullable()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_unitkerja_');
    }
}