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
        $this->call( kelas_table_seeder::class);
        $this->call( students_table_seeder::class);
        $this->call( daftar_table_seeder::class);
        $this->call( departments_table_seeder::class);
    }
}