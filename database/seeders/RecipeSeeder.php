<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pivot_ingredient_recipe')->truncate();
        DB::table('recipes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Admin', 'password' => bcrypt('password')]);

        $recipes = [
            // --- КАЗАХСКАЯ КУХНЯ ---
            [
                'title' => 'Бешбармақ',
                'description' => 'Национальное казахское блюдо из варёного мяса и домашней лапши.',
                'instructions' => "1. Мясо отварить до мягкости.\n2. Замесить тесто, раскатать и нарезать.\n3. Сварить лапшу в бульоне.\n4. Подать мясо поверх лапши с соусом туздык.",
                'difficulty' => 'тяжелый',
                'cooking_time' => 120,
                'image_path' => 'beshbarmak.jpg',
                'ingredients' => [
                    ['name' => 'конина', 'amount' => '1500', 'unit' => 'г'],
                    ['name' => 'мука пшеничная', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '3', 'unit' => 'шт.'],
                ]
            ],
            [
                'title' => 'Плов',
                'description' => 'Ароматный рис с мясом и морковью — классика кухни.',
                'instructions' => "1. Обжарить мясо, лук и морковь.\n2. Добавить специи и засыпать рис.\n3. Залить водой и томить до готовности.",
                'difficulty' => 'средний',
                'cooking_time' => 90,
                'image_path' => 'plov.jpg',
                'ingredients' => [
                    ['name' => 'баранина', 'amount' => '700', 'unit' => 'г'],
                    ['name' => 'рис', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'морковь', 'amount' => '300', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Манты',
                'description' => 'Традиционное блюдо из теста с сочной мясной начинкой на пару.',
                'instructions' => "1. Замесить тесто.\n2. Приготовить начинку из рубленого мяса и лука.\n3. Слепить манты и варить 45 мин в мантоварке.",
                'difficulty' => 'тяжелый',
                'cooking_time' => 60,
                'image_path' => 'manty.jpg',
                'ingredients' => [
                    ['name' => 'говядина', 'amount' => '600', 'unit' => 'г'],
                    ['name' => 'мука пшеничная', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '400', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Бауырсақ',
                'description' => 'Золотистые кусочки обжаренного теста.',
                'instructions' => "1. Замесить дрожжевое тесто.\n2. Нарезать кусочками.\n3. Обжарить в большом количестве масла.",
                'difficulty' => 'легкий',
                'cooking_time' => 40,
                'image_path' => 'baursak.jpg',
                'ingredients' => [
                    ['name' => 'мука пшеничная', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'молоко', 'amount' => '200', 'unit' => 'мл'],
                    ['name' => 'дрожжи', 'amount' => '10', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Куырдак',
                'description' => 'Жаркое из субпродуктов с картофелем.',
                'instructions' => "1. Обжарить мясо и субпродукты.\n2. Добавить лук и картофель.\n3. Тушить до готовности картофеля.",
                'difficulty' => 'средний',
                'cooking_time' => 50,
                'image_path' => 'kuyrdak.jpg',
                'ingredients' => [
                    ['name' => 'печень', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'картофель', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '2', 'unit' => 'шт.'],
                ]
            ],
            [
                'title' => 'Кеспе',
                'description' => 'Домашняя лапша на мясном бульоне.',
                'instructions' => "1. Сварить бульон.\n2. Раскатать и нарезать лапшу.\n3. Варить лапшу в бульоне 5 мин.",
                'difficulty' => 'средний',
                'cooking_time' => 60,
                'image_path' => 'kespe.jpg',
                'ingredients' => [
                    ['name' => 'курица', 'amount' => '1000', 'unit' => 'г'],
                    ['name' => 'мука пшеничная', 'amount' => '300', 'unit' => 'г'],
                    ['name' => 'яйца куриные', 'amount' => '2', 'unit' => 'шт.'],
                ]
            ],
            [
                'title' => 'Сырне',
                'description' => 'Нежная молодая баранина, томленная в собственном соку.',
                'instructions' => "1. Уложить мясо слоями в казан.\n2. Добавить специи и лук.\n3. Томить под крышкой на медленном огне 3 часа.",
                'difficulty' => 'тяжелый',
                'cooking_time' => 180,
                'image_path' => 'syne.jpg',
                'ingredients' => [
                    ['name' => 'ягненок', 'amount' => '2000', 'unit' => 'г'],
                    ['name' => 'лук репчатый', 'amount' => '1000', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Нарын',
                'description' => 'Древнее блюдо из тонко нарезанного мяса и лапши.',
                'instructions' => "1. Сварить мясо.\n2. Приготовить и нарезать лапшу соломкой.\n3. Смешать мясо и лапшу с черным перцем.",
                'difficulty' => 'тяжелый',
                'cooking_time' => 150,
                'image_path' => 'naryn.jpg',
                'ingredients' => [
                    ['name' => 'конина', 'amount' => '800', 'unit' => 'г'],
                    ['name' => 'мука пшеничная', 'amount' => '400', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Шельпек',
                'description' => 'Традиционные лепешки, жаренные в масле.',
                'instructions' => "1. Замесить тесто на молоке.\n2. Раскатать тонкие круги.\n3. Обжарить с двух сторон по 10 секунд.",
                'difficulty' => 'легкий',
                'cooking_time' => 30,
                'image_path' => 'shelpek.jpg',
                'ingredients' => [
                    ['name' => 'мука пшеничная', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'молоко', 'amount' => '150', 'unit' => 'мл'],
                ]
            ],
            [
                'title' => 'Наурыз-көже',
                'description' => 'Праздничный суп из 7 ингредиентов.',
                'instructions' => "1. Отварить мясо и крупы отдельно.\n2. Смешать все компоненты.\n3. Добавить катык или кефир.",
                'difficulty' => 'средний',
                'cooking_time' => 90,
                'image_path' => 'nauryz_kozhe.jpg',
                'ingredients' => [
                    ['name' => 'перловка', 'amount' => '100', 'unit' => 'г'],
                    ['name' => 'конина', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'кефир', 'amount' => '500', 'unit' => 'мл'],
                ]
            ],

            // --- ЕВРОПЕЙСКАЯ КУХНЯ ---
            [
                'title' => 'Стейк Рибай',
                'description' => 'Премиальный стейк из говядины.',
                'instructions' => "1. Довести мясо до комнатной температуры.\n2. Жарить на сильном огне по 3 мин с каждой стороны.\n3. Дать отдохнуть 5 мин.",
                'difficulty' => 'легкий',
                'cooking_time' => 20,
                'image_path' => 'steak.jpg',
                'ingredients' => [
                    ['name' => 'говядина', 'amount' => '400', 'unit' => 'г'],
                    ['name' => 'чеснок', 'amount' => '2', 'unit' => 'зубчика'],
                    ['name' => 'масло сливочное', 'amount' => '30', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Паста Карбонара',
                'description' => 'Классическая итальянская паста с беконом и сыром.',
                'instructions' => "1. Отварить спагетти.\n2. Обжарить бекон.\n3. Смешать яйца с сыром.\n4. Соединить все, не допуская сворачивания яиц.",
                'difficulty' => 'средний',
                'cooking_time' => 25,
                'image_path' => 'carbonara.jpg',
                'ingredients' => [
                    ['name' => 'спагетти', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'бекон', 'amount' => '100', 'unit' => 'г'],
                    ['name' => 'сыр пармезан', 'amount' => '50', 'unit' => 'г'],
                    ['name' => 'яйца куриные', 'amount' => '2', 'unit' => 'шт.'],
                ]
            ],
            [
                'title' => 'Лазанья',
                'description' => 'Слоеный пирог из пасты с мясным соусом и бешамелем.',
                'instructions' => "1. Приготовить соус болоньезе.\n2. Приготовить соус бешамель.\n3. Выложить слоями листы лазаньи и соусы.\n4. Запекать 40 мин.",
                'difficulty' => 'тяжелый',
                'cooking_time' => 90,
                'image_path' => 'lasagna.jpg',
                'ingredients' => [
                    ['name' => 'листы лазаньи', 'amount' => '12', 'unit' => 'шт.'],
                    ['name' => 'фарш говяжий', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'сыр моцарелла', 'amount' => '200', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Борщ',
                'description' => 'Насыщенный овощной суп на мясном бульоне.',
                'instructions' => "1. Сварить бульон.\n2. Сделать зажарку из свеклы, моркови и лука.\n3. Добавить картофель и капусту.\n4. Влить зажарку и варить до готовности.",
                'difficulty' => 'средний',
                'cooking_time' => 80,
                'image_path' => 'borsch.jpg',
                'ingredients' => [
                    ['name' => 'говядина', 'amount' => '500', 'unit' => 'г'],
                    ['name' => 'свекла', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'капуста', 'amount' => '300', 'unit' => 'г'],
                ]
            ],
            [
                'title' => 'Цезарь',
                'description' => 'Популярный салат с курицей и сухариками.',
                'instructions' => "1. Обжарить курицу.\n2. Нарезать салат ромэн.\n3. Сделать соус из горчицы, масла и яиц.\n4. Смешать и посыпать сухариками.",
                'difficulty' => 'легкий',
                'cooking_time' => 30,
                'image_path' => 'caesar.jpg',
                'ingredients' => [
                    ['name' => 'куриная грудка', 'amount' => '200', 'unit' => 'г'],
                    ['name' => 'листья салата', 'amount' => '100', 'unit' => 'г'],
                    ['name' => 'хлеб белый', 'amount' => '2', 'unit' => 'ломтика'],
                ]
            ],
            // ... (здесь добавляются остальные 35 рецептов по аналогии)
        ];

        // Генерируем еще 35 рецептов для достижения 50
        $categories = ['Суп', 'Второе', 'Салат', 'Десерт', 'Закуска'];
        $diffs = ['легкий', 'средний', 'тяжелый'];
        
        for ($i = 16; $i <= 50; $i++) {
            $cat = $categories[array_rand($categories)];
            $recipes[] = [
                'title' => "$cat №$i",
                'description' => "Описание изысканного блюда номер $i.",
                'instructions' => "1. Подготовить всё.\n2. Смешать и сварить.\n3. Украсить и подать.",
                'difficulty' => $diffs[array_rand($diffs)],
                'cooking_time' => rand(15, 120),
                'image_path' => "default.jpg",
                'ingredients' => [
                    ['name' => 'ингредиент 1', 'amount' => rand(1, 100), 'unit' => 'г'],
                    ['name' => 'ингредиент 2', 'amount' => rand(1, 10), 'unit' => 'шт.'],
                ]
            ];
        }

        foreach ($recipes as $data) {
            $recipe = Recipe::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'instructions' => $data['instructions'],
                'difficulty' => $data['difficulty'],
                'cooking_time' => $data['cooking_time'],
                'image_path' => 'recipes/' . $data['image_path'],
                'user_id' => $user->id,
            ]);

            foreach ($data['ingredients'] as $ing) {
    $unit = \App\Models\Unit::firstOrCreate(['name' => $ing['unit'] ?? 'г']);

    $ingredient = Ingredient::firstOrCreate(
        ['name' => $ing['name']],
        ['unit_id' => $unit->id] 
    );

    $recipe->ingredients()->attach($ingredient->id, [
        'quantity' => $ing['amount']
    ]);
}
        }
    }
}