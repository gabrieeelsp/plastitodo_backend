<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class PaymentmethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('paymentmethods')->insert([
            'name' => "Efectivo"
        ]);

        /* DB::table('paymentmethods')->insert([
            'name' => "Débito"
        ]);

        DB::table('paymentmethods')->insert([
            'name' => "Crédito"
        ]);

        DB::table('paymentmethods')->insert([
            'name' => "Transferencia",
            'requires_confirmation' => true,
        ]);

        DB::table('paymentmethods')->insert([
            'name' => "QR"
        ]); */
    }
}
