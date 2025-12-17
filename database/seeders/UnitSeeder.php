<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'г',
            'кг', 
            'мг', 
            'мл', 
            'л',  
            'ст.л.',   
            'ч.л.',  
            'дес.л.',  
            'шт.',   
            'горсть',    
            'щепотка',   
            'стакан',    
            'по вкусу',  
            'на кончике ножа',
            'пучок',    
            'веточка', 
            'капля',      
            'упаковка',  
            'банка',     
            'пласт',     
        ];

        DB::table('units')->truncate();

        foreach ($units as $name) {
            DB::table('units')->insert([
                'name' => $name,
            ]);
        }
    }
}
