<?php

use Illuminate\Database\Seeder;

class schedules_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('schedules')->truncate();

        // insert batch
        DB::table('schedules')->insert($schedules);
    }
}
