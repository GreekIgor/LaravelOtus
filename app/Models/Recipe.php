<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\ValueObjects\RecipeTitle;

class Recipe extends Model
{
    //

    // Запрещаем массовое присвоение, переходим на фабрики/доменные методы
    protected $fillable = ['title', 'cooking_time', 'difficulty', 'image_path', 'ingredients', 'amounts', 'units', 'instructions', 'user_id'];

    // Допустимые уровни сложности (синхронизировано с сидером)
    public const DIFFICULTY_EASY = 'легкий';
    public const DIFFICULTY_MEDIUM = 'средний';
    public const DIFFICULTY_HARD = 'тяжелый';

    /**
     * Фабрика защищенного создания рецепта
     */
    public static function new(
        RecipeTitle $title,
        ?string $description,
        ?string $instructions,
        ?string $imagePath,
        string $difficulty,
        int $cookingTime,
        int $userId
    ): self {
        self::assertDifficulty($difficulty);
        if ($cookingTime <= 0) {
            throw new \InvalidArgumentException('Cooking time must be positive');
        }

        $recipe = new self();
        $recipe->attributes['title'] = $title->value();
        $recipe->attributes['description'] = $description;
        $recipe->attributes['instructions'] = $instructions;
        $recipe->attributes['image_path'] = $imagePath;
        $recipe->attributes['difficulty'] = $difficulty;
        $recipe->attributes['cooking_time'] = $cookingTime;
        $recipe->attributes['user_id'] = $userId;
        $recipe->save();

        return $recipe;
    }

    /**
     * Доменные геттеры (вместо магических свойств)
     */
    public function getId(): int { return (int) ($this->attributes['id'] ?? 0); }
    public function getTitle(): string { return (string) ($this->attributes['title'] ?? ''); }
    public function getDescription(): ?string { return $this->attributes['description'] ?? null; }
    public function getInstructions(): ?string { return $this->attributes['instructions'] ?? null; }
    public function getImagePath(): ?string { return $this->attributes['image_path'] ?? null; }
    public function getDifficulty(): string { return (string) ($this->attributes['difficulty'] ?? self::DIFFICULTY_MEDIUM); }
    public function getCookingTime(): int { return (int) ($this->attributes['cooking_time'] ?? 0); }
    public function getAuthorId(): int { return (int) ($this->attributes['user_id'] ?? 0); }

    /**
     * Доменные мутации без сеттеров
     */
    public function rename(RecipeTitle $title): void
    {
        $this->attributes['title'] = $title->value();
        $this->save();
    }

    public function changeDifficulty(string $difficulty): void
    {
        self::assertDifficulty($difficulty);
        $this->attributes['difficulty'] = $difficulty;
        $this->save();
    }

    public function changeCookingTime(int $minutes): void
    {
        if ($minutes <= 0) {
            throw new \InvalidArgumentException('Cooking time must be positive');
        }
        $this->attributes['cooking_time'] = $minutes;
        $this->save();
    }

    public function updateInstructions(?string $instructions): void
    {
        $this->attributes['instructions'] = $instructions;
        $this->save();
    }

    public function updateDescription(?string $description): void
    {
        $this->attributes['description'] = $description;
        $this->save();
    }

    public function changeImagePath(?string $imagePath): void
    {
        $this->attributes['image_path'] = $imagePath;
        $this->save();
    }

    private static function assertDifficulty(string $difficulty): void
    {
        $allowed = [self::DIFFICULTY_EASY, self::DIFFICULTY_MEDIUM, self::DIFFICULTY_HARD];
        if (!in_array($difficulty, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid difficulty');
        }
    }
    
    /**
     * Получить маршрутный ключ для модели.
     * Это гарантирует, что route model binding будет использовать ID.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
    
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'pivot_ingredient_recipe',
            'recipe_id',
            'ingredient_id'
        )->withPivot('quantity');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
