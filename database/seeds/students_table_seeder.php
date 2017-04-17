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
            ['id' => 1, 'name' => 'dea', 'nis' => '1', 'kelas' => '1', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'nis' => '2', 'kelas' => '2', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'nis' => '3', 'kelas' => '3', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'nis' => '4', 'kelas' => '4', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi', 'nis' => '5', 'kelas' => '5', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'nis' => '6', 'kelas' => '6', 'departments' => 'rpl', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('students')->insert($students);
    }
}
