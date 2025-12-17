<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->truncate();
        
       DB::table('tags')->insert([
           ['name' => 'Казахская кухня'],
           ['name' => 'Мясные блюда'],
           ['name' => 'Национальные блюда'],
           ['name' => 'Десерты'],
           ['name' => 'Блюда на пару'],
       ]);
    }
}
