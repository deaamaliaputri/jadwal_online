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
            ['id' => 1, 'name' => 'ELEKTRO 1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'ELEKTRO 2', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'ELEKTRO 3', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'RPL 1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'RPL 2',  'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'RPL 3', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 7, 'name' => 'TKJ 1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 8, 'name' => 'TKJ 2', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 9, 'name' => 'TKR 1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 10, 'name' => 'TKR 2', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 11, 'name' => 'TKR 3', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 12, 'name' => 'TSM 1', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 13, 'name' => 'TSM 2', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('departments')->insert($departments);
    }
}
