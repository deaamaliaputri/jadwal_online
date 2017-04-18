<?php

use Illuminate\Database\Seeder;

class students_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('students')->truncate();

        $students = [
            ['id' => 1, 'name' => 'dea', 'nis' => '1', 'kelas_id' => '1', 'departments_id' => '1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'nis' => '2', 'kelas_id' => '2', 'departments_id' => '2', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'nis' => '3', 'kelas_id' => '3', 'departments_id' => '3', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'nis' => '4', 'kelas_id' => '4', 'departments_id' => '4', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi', 'nis' => '5', 'kelas_id' => '5', 'departments_id' => '5', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'nis' => '6', 'kelas_id' => '6', 'departments_id' => '6', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('students')->insert($students);
    }
}
