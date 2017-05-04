<?php

use Illuminate\Database\Seeder;

class daftars_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
 DB::table('daftars')->truncate();

        $daftars = [
            ['id' => 1, 'name' => 'dea', 'email' => 'dea@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345','status' => '0', 'level' => '0', 'nip' => '11167', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'email' => 'amalia@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345', 'status' => '1', 'level' => '1', 'nip' => '435', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'email' => 'putri@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345', 'status' => '0', 'level' => '0', 'nip' => '243556', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'email' => 'cintya@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345', 'status' => '1', 'level' => '1', 'nip' => '2323', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi', 'email' => 'dewi@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345', 'status' => '0', 'level' => '0', 'nip' => '5656', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'email' => 'nur@gmail.com', 'password' => bcrypt('12345678'), 'phone' => '08912345', 'status' => '1', 'level' => '1', 'nip' => '2324', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('daftars')->insert($daftars);
    }
}
