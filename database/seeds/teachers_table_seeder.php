<?php

use Illuminate\Database\Seeder;

class teachers_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate record
        DB::table('teachers')->truncate();

        $teachers = [
            ['id' => 1, 'name' => 'Bu. Nadhira', 'nip' => '101', 'kode' => '10', 'phone' => '08912345678', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 2, 'name' => 'Bpk. Wasum', 'nip' => '102', 'kode' => '20', 'phone' => '08812345672', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 3, 'name' => 'Bu. Fitri', 'nip' => '103', 'kode' => '30', 'phone' => '0871239835', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 4, 'name' => 'Bpk. Eka', 'nip' => '104', 'kode' => '40', 'phone' => '0819237765', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 5, 'name' => 'Bu. Duwi', 'nip' => '105', 'kode' => '50', 'phone' => '089523467812', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 6, 'name' => 'Bpk. Bagus', 'nip' => '106', 'kode' => '60', 'phone' => '0823522346', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 7, 'name' => 'Bpk. Niko', 'nip' => '107', 'kode' => '70', 'phone' => '0811234512', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 8, 'name' => 'Bu. Anis', 'nip' => '108', 'kode' => '80', 'phone' => '089083422', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 9, 'name' => 'Bpk. Nur', 'nip' => '109', 'kode' => '90', 'phone' => '0845732446', 'created_at' => \Carbon\Carbon::now()],
            ['id' => 10, 'name' => 'Bpk. Listmawati', 'nip' => '110', 'kode' => '100', 'phone' => '0899993333', 'created_at' => \Carbon\Carbon::now()],
        ];

        // insert batch
        DB::table('teachers')->insert($teachers);
    }
}
