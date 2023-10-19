<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'id' => '001',
            'name' => 'Machine',
        ]);
        DB::table('categories')->insert([
            'id' => '002',
            'name' => 'Food',
        ]);
        DB::table('categories')->insert([
            'id' => '003',
            'name' => 'Gadget',
        ]);
        DB::table('categories')->insert([
            'id' => '004',
            'name' => 'Monitor',
        ]);
        DB::table('categories')->insert([
            'id' => '005',
            'name' => 'Motocycle',
        ]);
        DB::table('categories')->insert([
            'id' => '006',
            'name' => 'Cooks',
        ]);
        DB::table('categories')->insert([
            'id' => '007',
            'name' => 'Plant s',
        ]);
        DB::table('categories')->insert([
            'id' => '008',
            'name' => 'Monitor',
        ]);
    }
}
