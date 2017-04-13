<?php

use Illuminate\Database\Seeder;

class teachers_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('teachers')->truncate();

        $teachers = [
            ['id' => 1, 'name' => 'dea', 'nip' => '1627', 'kode' => '1', 'phone' => '089555', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'nip' => '2778', 'kode' => '2', 'phone' => '089666', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'nip' => '3784', 'kode' => '3', 'phone' => '08933', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'nip' => '4738', 'kode' => '4', 'phone' => '089111', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi', 'nip' => '5837', 'kode' => '5', 'phone' => '089777', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'nip' => '6832', 'kode' => '6', 'phone' => '089999', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('teachers')->insert($teachers);
    }
}
