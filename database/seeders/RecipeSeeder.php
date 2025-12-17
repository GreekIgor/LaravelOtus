<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           $recipes = [
            [
                'title' => 'Бешбармақ',
                'description' => 'Национальное казахское блюдо из варёного мяса и домашней лапши.',
                'instructions' => "1. Мясо промойте, залейте холодной водой, доведите до кипения, слейте воду (удалите пену).\n2. Залейте чистой водой, варите 1.5–2 часа до мягкости.\n3. Для теста: смешайте муку, яйцо, соль, воду. Раскатайте, нарежьте ромбиками.\n4. Лук мелко нарежьте, залейте горячим бульоном — получится «тұздық».\n5. Подавайте: лапша → мясо → лук с бульоном.",
                'ingredients' => [
                    ['name' => 'баранина', 'amount' => '1000', 'unit' => 'г'],
                    ['name' => 'мука пшеничная', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '3', 'unit' => 'шт.'],
                    ['name' => 'яйца куриные', 'amount' => '1', 'unit' => 'шт.'],
                    ['name' => 'соль', 'amount' => 'по вкусу', 'unit' => 'по вкусу'],
                ]
            ],
            [
                'title' => 'Плов',
                'description' => 'Ароматный рис с мясом и морковью — классика казахской кухни.',
                'instructions' => "1. Мясо нарежьте кубиками, обжарьте в котле на жиру.\n2. Добавьте крупно нарезанный лук, жарьте 5 мин.\n3. Добавьте морковь соломкой, жарьте 10 мин.\n4. Положите промытый рис, залейте кипятком (на 2 см выше риса).\n5. Готовьте 20–25 мин под крышкой. Дайте настояться 15 мин.",
                'ingredients' => [
                    ['name' => 'баранина', 'amount' => '700', 'unit' => 'г'],
                    ['name' => 'рис', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'морковь', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '2', 'unit' => 'шт.'],
                    ['name' => 'чеснок', 'amount' => '1', 'unit' => 'головка'],
                    ['name' => 'зира (кумин)', 'amount' => '1', 'unit' => 'ч.л.'],
                    ['name' => 'соль', 'amount' => 'по вкусу', 'unit' => 'по вкусу'],
                    ['name' => 'жир (курдючный или говяжий)', 'amount' => '100', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Манты',
                'description' => 'Паровые пельмени с мясной начинкой — сытно и вкусно.',
                'instructions' => "1. Для теста: мука + вода + соль → замесить, дать отдохнуть 30 мин.\n2. Для начинки: фарш (мясо + лук), соль, перец, зира.\n3. Раскатайте тесто, нарежьте квадратами, положите фарш, слепите конвертики.\n4. Готовьте на пару 40–50 мин.\n5. Подавайте со сметаной или томатным соусом.",
                'ingredients' => [
                    ['name' => 'мука пшеничная', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'баранина', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'соль', 'amount' => '1', 'unit' => 'ч.л.'],
                    ['name' => 'перец чёрный молотый', 'amount' => '0.5', 'unit' => 'ч.л.'],
                    ['name' => 'зира (кумин)', 'amount' => '1', 'unit' => 'ч.л.'],
                ]
            ],
            [
                'title' => 'Бауырсақ',
                'description' => 'Жареные шарики из пресного теста — традиционный десерт.',
                'instructions' => "1. Смешайте муку, соль, соду. Добавьте кефир и яйцо, замесите тесто.\n2. Скатайте шарики размером с грецкий орех.\n3. Обжаривайте во фритюре до золотистого цвета.\n4. Обваляйте в мёде или сахарной пудре.",
                'ingredients' => [
                    ['name' => 'мука пшеничная', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'кефир', 'amount' => '200', 'unit' => 'мл'],
                    ['name' => 'яйца куриные', 'amount' => '1', 'unit' => 'шт.'],
                    ['name' => 'соль', 'amount' => '0.5', 'unit' => 'ч.л.'],
                    ['name' => 'сода', 'amount' => '0.5', 'unit' => 'ч.л.'],
                    ['name' => 'масло растительное', 'amount' => '200', 'unit' => 'мл'],
                    ['name' => 'мёд', 'amount' => '100', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Құртты сорпа (Суп с куртом)',
                'description' => 'Лёгкий кисломолочный суп с куртом и зеленью.',
                'instructions' => "1. Курт залейте водой, дайте размокнуть 10 мин.\n2. Протрите через сито или измельчите блендером.\n3. Добавьте рубленую зелень, соль, перец.\n4. Подавайте охлаждённым.",
                'ingredients' => [
                    ['name' => 'курт (солёный иримшик)', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'укроп', 'amount' => '30', 'unit' => 'г'],
                    ['name' => 'петрушка', 'amount' => '20', 'unit' => 'г'],
                    ['name' => 'кинза', 'amount' => '20', 'unit' => 'г'],
                    ['name' => 'вода', 'amount' => '1', 'unit' => 'л'],
                    ['name' => 'соль', 'amount' => 'по вкусу', 'unit' => 'по вкусу'],
                ]
            ],
            [
                'title' => 'Шұжық',
                'description' => 'Копчёная конская колбаса — деликатес.',
                'ingredients' => [
                    ['name' => 'конина', 'amount' => '1000', 'unit' => 'г'],
                    ['name' => 'жир (курдючный)', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'соль', 'amount' => '30', 'unit' => 'г'],
                    ['name' => 'чеснок', 'amount' => '5', 'unit' => 'зубчиков'],
                    ['name' => 'перец чёрный молотый', 'amount' => '1', 'unit' => 'ч.л.'],
                ]
            ],
            [
                'title' => 'Қазы',
                'description' => 'Варёно-копчёная колбаса из конской рёбрышки.',
                'ingredients' => [
                    ['name' => 'конина (рёбрышки)', 'amount' => '1500', 'unit' => 'г'],
                    ['name' => 'соль', 'amount' => '50', 'unit' => 'г'],
                    ['name' => 'лавровый лист', 'amount' => '2', 'unit' => 'шт.'],
                    ['name' => 'перец чёрный горошком', 'amount' => '5', 'unit' => 'горошин'],
                ]
            ],
            [
                'title' => 'Көже',
                'description' => 'Густой мясной суп с крупой и тестом.',
                'ingredients' => [
                    ['name' => 'баранина', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'пшено', 'amount' => '100', 'unit' => 'г'],
                    ['name' => 'картофель', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '1', 'unit' => 'шт.'],
                    ['name' => 'тесто для көже', 'amount' => '150', 'unit' => 'г'],
                    ['name' => 'соль', 'amount' => 'по вкусу', 'unit' => 'по вкусу'],
                ]
            ],
            [
                'title' => 'Лагман',
                'description' => 'Узбекско-казахская лапша с мясом и овощами.',
                'ingredients' => [
                    ['name' => 'баранина', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'лапша', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'морковь', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '1', 'unit' => 'шт.'],
                    ['name' => 'помидоры', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'болгарский перец', 'amount' => '1', 'unit' => 'шт.'],
                    ['name' => 'чеснок', 'amount' => '3', 'unit' => 'зубчиков'],
                    ['name' => 'зира (кумин)', 'amount' => '0.5', 'unit' => 'ч.л.'],
                ]
            ],
            [
                'title' => 'Сүзбе',
                'description' => 'Свежий творог из простокваши — основа многих блюд.',
                'ingredients' => [
                    ['name' => 'молоко', 'amount' => '2', 'unit' => 'л'],
                    ['name' => 'кефир', 'amount' => '100', 'unit' => 'мл'],
                    ['name' => 'соль', 'amount' => 'по вкусу', 'unit' => 'по вкусу'],
                ]
            ],
        ];

        DB::table('pivot_ingredient_recipe')->delete();
        DB::table('recipes')->delete();

        $user = User::firstOrCreate(['email' => 'admin@example.com', 'name' => 'Admin', 'password' => bcrypt('password')]);


         foreach ($recipes as $data)    
            {
                $recipe = \App\Models\Recipe::create([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'instructions' => $data['instructions'] ?? '',
                    'user_id' => $user->id, // Assuming user with ID 1 exists
                ]);
    
                if (isset($data['ingredients'])) {
                    foreach ($data['ingredients'] as $ing) {
                        $ingredient = \App\Models\Ingredient::where('name', $ing['name'])->first();
                            if ($ingredient) {
                                    $recipe->ingredients()->attach($ingredient->id, [
                                        'quantity' => $ing['amount'],
                                    ]);
                                }
                    }
                }
            }

    }
}
