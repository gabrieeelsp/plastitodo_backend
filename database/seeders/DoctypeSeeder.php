<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('doctypes')->insert([
            'id_afip' => 80,
            'name' => 'CUIT'
        ]);

        DB::table('doctypes')->insert([
            'id_afip' => 86,
            'name' => 'CUIL'
        ]);

        DB::table('doctypes')->insert([
            'id_afip' => 96,
            'name' => 'DNI'
        ]);

        DB::table('doctypes')->insert([
            'id_afip' => 99,
            'name' => 'Sin identificar'
        ]);
    }
}
