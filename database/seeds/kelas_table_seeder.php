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
            ['id' => 1, 'name' => 'dea', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi',  'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('kelas')->insert($kelas);
    }
}
