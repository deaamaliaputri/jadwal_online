<?php

use Illuminate\Database\Seeder;

class subjects_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('subjects')->truncate();

        $subjects = [
            ['id' => 1, 'name' => 'agama', 'teachers_id' => '1', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'fisika', 'teachers_id' => '2', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'ipa', 'teachers_id' => '3', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'ips', 'teachers_id' => '4', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'kewirausahaan', 'teachers_id' => '5', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'komputer', 'teachers_id' => '6', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('subjects')->insert($subjects);
    }
}
