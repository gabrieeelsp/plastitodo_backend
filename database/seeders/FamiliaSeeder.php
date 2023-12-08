<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class FamiliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('familias')->insert([
            'name' => 'Dulde de Leche',
        ]);
        DB::table('familias')->insert([
            'name' => 'Alfajores',
        ]);
        DB::table('familias')->insert([
            'name' => 'Chupetines',
        ]);
    }
}
