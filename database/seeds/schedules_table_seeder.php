<?php

use Illuminate\Database\Seeder;

class schedules_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('schedules')->truncate();

        $schedules = [
            ['id' => 1, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '1', 'teachers_id' => '1', 'departments_id' => '1', 'class_id' => '1','created_at'=> \Carbon\Carbon::now()],
            ['id' => 2, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '2', 'teachers_id' => '2', 'departments_id' => '2', 'class_id' => '2','created_at'=> \Carbon\Carbon::now()],
            ['id' => 3, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '3', 'teachers_id' => '3', 'departments_id' => '3', 'class_id' => '3','created_at'=> \Carbon\Carbon::now()],
            ['id' => 4, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '4', 'teachers_id' => '4', 'departments_id' => '4', 'class_id' => '4','created_at'=> \Carbon\Carbon::now()],
            ['id' => 5, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '5', 'teachers_id' => '5', 'departments_id' => '5', 'class_id' => '5','created_at'=> \Carbon\Carbon::now()],
            ['id' => 6, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '6', 'teachers_id' => '6', 'departments_id' => '6', 'class_id' => '6','created_at'=> \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('schedules')->insert($schedules);
    }
}
