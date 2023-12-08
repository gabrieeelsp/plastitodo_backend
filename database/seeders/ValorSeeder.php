<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class ValorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('valors')->insert([
            'name' => "Billete de $1000",
            "valor" => 1000
        ]);
        DB::table('valors')->insert([
            'name' => "Billete de $500",
            "valor" => 500
        ]);
        DB::table('valors')->insert([
            'name' => "Billete de $200",
            "valor" => 200
        ]);
        DB::table('valors')->insert([
            'name' => "Billete de $100",
            "valor" => 100
        ]);
        DB::table('valors')->insert([
            'name' => "Billete de $50",
            "valor" => 50
        ]);
        DB::table('valors')->insert([
            'name' => "Billete de $10",
            "valor" => 10
        ]);

        DB::table('valors')->insert([
            'name' => "Moneda de $10",
            "valor" => 10
        ]);

        DB::table('valors')->insert([
            'name' => "Moneda de $5",
            "valor" => 5
        ]);

        DB::table('valors')->insert([
            'name' => "Moneda de $1",
            "valor" => 1
        ]);

        DB::table('valors')->insert([
            'name' => "Moneda de $0,50",
            "valor" => 0.5
        ]);

        DB::table('valors')->insert([
            'name' => "Moneda de $0,25",
            "valor" => 0.25
        ]);
    }
}
