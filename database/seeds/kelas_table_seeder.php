<?php

use Illuminate\Database\Seeder;

class kelas_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        // truncate record
        DB::table('kelas')->truncate();

        $kelas = [
            ['id' => 1, 'name' => '10', 'descriptions' => '40 siswa', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => '11', 'descriptions' => '40 siswa', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => '12', 'descriptions' => '40 siswa', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('kelas')->insert($kelas);
    }
}
