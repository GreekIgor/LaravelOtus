<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cooking_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipe_id');
            $table->date('planned_date');
            $table->time('planned_time')->nullable();
            $table->integer('servings')->default(1);
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            // Индекс для быстрого поиска по дате и пользователю
            $table->index(['user_id', 'planned_date']);
            $table->index('recipe_id');
            $table->index('planned_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooking_plans');
    }
};
