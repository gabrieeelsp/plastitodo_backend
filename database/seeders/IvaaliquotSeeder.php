<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class IvaaliquotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ivaaliquots')->insert([
            'name' => 'No Gravado',
            'valor' => 0,
            'id_afip' => '1'
        ]);
        DB::table('ivaaliquots')->insert([
            'name' => 'Exento',
            'valor' => 0,
            'id_afip' => '2'
        ]);
        DB::table('ivaaliquots')->insert([
            'name' => '10,50',
            'valor' => 10.5,
            'id_afip' => '4'
        ]);
        DB::table('ivaaliquots')->insert([
            'name' => '21,00',
            'valor' => 21,
            'id_afip' => '5'
        ]);
        
        DB::table('ivaaliquots')->insert([
            'name' => '0',
            'valor' => 0,
            'id_afip' => '3'
        ]);
        
    }
}
