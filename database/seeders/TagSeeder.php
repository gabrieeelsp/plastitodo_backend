<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->insert([
            'name' => 'Repostería',
            'color' => 'red'
        ]);
        DB::table('tags')->insert([
            'name' => 'Rotisería',
            'color' => 'blue'
        ]);
        DB::table('tags')->insert([
            'name' => 'Panadería',
            'color' => 'green'
        ]);
    }
}
