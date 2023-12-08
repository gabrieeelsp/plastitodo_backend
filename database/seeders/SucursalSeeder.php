<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sucursals')->insert([
            'name' => "Casa central",
            "empresa_id" => 1,
            'direccion' => "Baigorria 1306",
            'punto_venta_fe' => 5,
        ]);
        
        /* DB::table('sucursals')->insert([
            'name' => "Rosario Centro",
            "empresa_id" => 1,
            'direccion' => "Cordoba 3427",
            'punto_venta_fe' => 6,
        ]); */
/*
        DB::table('sucursals')->insert([
            'name' => "Rosario Sur",
            "empresa_id" => 1,
            'direccion' => "San Martin 3427",
            'punto_venta_fe' => 7,
        ]); */
    }
}
