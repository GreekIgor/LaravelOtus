<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('tags')->insert([
           ['name' => 'Vegetarian'],
           ['name' => 'Vegan'],
           ['name' => 'Gluten-Free'],
           ['name' => 'Dessert'],
           ['name' => 'Quick & Easy'],
       ]);
    }
}
