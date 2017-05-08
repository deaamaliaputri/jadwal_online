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
            ['id' => 1, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '1','wali_kelas' => '1', 'hari' => 'senin', 'subjects_id' => '1', 'teachers_id' => '1', 'departments_id' => '1', 'kelas_id' => '1','created_at'=> \Carbon\Carbon::now()],
            ['id' => 2, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '2','wali_kelas' => '2', 'hari' => 'selasa', 'subjects_id' => '2', 'teachers_id' => '2', 'departments_id' => '2', 'kelas_id' => '2','created_at'=> \Carbon\Carbon::now()],
            ['id' => 3, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '3','wali_kelas' => '3', 'hari' => 'rabu', 'subjects_id' => '3', 'teachers_id' => '3', 'departments_id' => '3', 'kelas_id' => '3','created_at'=> \Carbon\Carbon::now()],
            ['id' => 4, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '4','wali_kelas' => '4', 'hari' => 'kamis', 'subjects_id' => '4', 'teachers_id' => '4', 'departments_id' => '4', 'kelas_id' => '4','created_at'=> \Carbon\Carbon::now()],
            ['id' => 5, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '5','wali_kelas' => '5', 'hari' => 'jumat', 'subjects_id' => '5', 'teachers_id' => '5', 'departments_id' => '5', 'kelas_id' => '5','created_at'=> \Carbon\Carbon::now()],
            ['id' => 6, 'time' => \Carbon\Carbon::now(), 'hour' => \Carbon\Carbon::now(), 'room' => '6','wali_kelas' => '6', 'hari' => 'sabtu', 'subjects_id' => '6', 'teachers_id' => '6', 'departments_id' => '6', 'kelas_id' => '6','created_at'=> \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('schedules')->insert($schedules);
    }
}
