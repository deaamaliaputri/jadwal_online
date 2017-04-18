<?php

use Illuminate\Database\Seeder;

class departments_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('departments')->truncate();

        $departments = [
            ['id' => 1, 'name' => 'tkr', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'elekro', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'RPL', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'sepeda motor', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'jaringan',  'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'animasi', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('departments')->insert($departments);
    }
}
