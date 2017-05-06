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
            $table->string('hari');
            $table->string('subjects_id');
            $table->string('teachers_id');
            $table->string('departments_id');
            $table->string('kelas_id');
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
