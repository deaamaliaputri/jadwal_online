<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->date('time');
            $table->date('hour');
            $table->string('room');
            $table->integer('teachers_id', false);
            $table->integer('departments_id', false);
            $table->integer('kelas_id', false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     Schema::drop('schedules');

    }
}
