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
            ['id' => 1, 'name' => 'Dea Amalia Putri', 'nis' => '1001', 'kelas_id' => '1', 'departments_id' => '1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'Amalia Putri Dea', 'nis' => '1002', 'kelas_id' => '2', 'departments_id' => '2', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'Putri Dea Amalia', 'nis' => '1003', 'kelas_id' => '3', 'departments_id' => '3', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'Cintya Nur Dewi', 'nis' => '1004', 'kelas_id' => '1', 'departments_id' => '4', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'Dewi Cintya', 'nis' => '1005', 'kelas_id' => '2', 'departments_id' => '5', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'Nur Lailiyah', 'nis' => '1006', 'kelas_id' => '3', 'departments_id' => '6', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 7, 'name' => 'Khamilatul Zahro', 'nis' => '1007', 'kelas_id' => '1', 'departments_id' => '7', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 8, 'name' => 'Della Amanda', 'nis' => '1008', 'kelas_id' => '2', 'departments_id' => '8', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 9, 'name' => 'Hasan Basri', 'nis' => '1009', 'kelas_id' => '3', 'departments_id' => '9', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 10, 'name' => 'Syahrul Ubaidillah', 'nis' => '1010', 'kelas_id' => '1', 'departments_id' => '10', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 11, 'name' => 'Edi Santoso', 'nis' => '1011', 'kelas_id' => '2', 'departments_id' => '11', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 12, 'name' => 'Hemy Yahya', 'nis' => '1012', 'kelas_id' => '3', 'departments_id' => '12', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 13, 'name' => 'Megha Ahmad Zeni', 'nis' => '1013', 'kelas_id' => '1', 'departments_id' => '13', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('students')->insert($students);
    }
}
