<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class CajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cajas')->insert([
            'dinero_inicial' => 80,
            'is_open' => true,
            'user_id' => 1,
            'sucursal_id' => 1
        ]);
    }
}
