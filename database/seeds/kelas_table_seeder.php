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
            ['id' => 1, 'name' => '10 A', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => '10 B', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => '11 A', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => '11 B', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => '12 A',  'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => '12 B', 'descriptions' => 'ada', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('kelas')->insert($kelas);
    }
}
