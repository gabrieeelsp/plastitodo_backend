<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class DeliveryshiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deliveryshifts')->insert([
            'name' => 'Mañana',
            'description' => 'de 11:00 a 13:00',
        ]);
        DB::table('deliveryshifts')->insert([
            'name' => 'Tarde',
            'description' => 'de 15:00 a 17:00',
        ]);
    }
}
