<?php

use Illuminate\Database\Seeder;

class users_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
public function run()
    {
        // truncate record
        DB::table('users')->truncate();

        $users = [
            ['id' => 1, 'name' => 'dea', 'email' => 'dea@gmail.com', 'password' => '1234', 'phone' => '089222', 'status' => 'guru', 'level' => 'ada', 'nip' => '18823' 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'amalia', 'email' => 'amalia@gmail.com', 'password' => '234', 'phone' => '089666', 'status' => 'murid', 'level' => 'ada', 'nip' => '234' 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'putri', 'email' => 'putri@gmail.com', 'password' => '345', 'phone' => '08933', 'status' => 'guru', 'level' => 'ada', 'nip' => '345' 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'cintya', 'email' => '4cintya@gmail.com738', 'password' => '456', 'phone' => '089111', 'status' => 'level' => 'ada', 'murid', 'nip' => '456' 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'dewi', 'email' => 'dewi@gmail.com', 'password' => '567', 'phone' => '089777', 'status' => 'guru', 'level' => 'ada', 'nip' => '567' 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'nur', 'email' => 'nur@gmail.com', 'password' => '678', 'phone' => '089999', 'status' => 'murid', 'level' => 'ada', 'nip' => '678' 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('users')->insert($users);
    }
}
