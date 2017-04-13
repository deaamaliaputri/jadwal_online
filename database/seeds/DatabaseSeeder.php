<?php
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ContactTableSeeder::class);
        $this->call( schedules_table_seeder::class);
        $this->call( teachers_table_seeder::class);
        $this->call( subjects_table_seeder::class);
        $this->call( users_table_seeder::class);
    }
}

