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
            ['id' => 1, 'name' => 'Agama', 'teachers_id' => '1', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'Fisika', 'teachers_id' => '2', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'IPA', 'teachers_id' => '3', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'IPS', 'teachers_id' => '4', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'Kewirausahaan', 'teachers_id' => '5', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'Komputer', 'teachers_id' => '6', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 7, 'name' => 'PKN', 'teachers_id' => '7', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 8, 'name' => 'Seni Budaya', 'teachers_id' => '8', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 9, 'name' => 'Matematematika', 'teachers_id' => '9', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 10, 'name' => 'Bahasa Indonesia', 'teachers_id' => '10', 'descriptions' => 'masuk', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('subjects')->insert($subjects);
    }
}
