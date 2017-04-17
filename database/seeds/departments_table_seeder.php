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
            ['id' => 1, 'name' => 'ipa', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'ips', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'fisika', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'mtk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'bhs indo',  'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'agama', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('departments')->insert($departments);
    }
}
