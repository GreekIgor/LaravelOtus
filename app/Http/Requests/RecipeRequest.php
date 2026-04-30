<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $difficulty = $this->input('difficulty');

        if (is_string($difficulty)) {
            $normalized = mb_strtolower(trim($difficulty));

            $map = [
                'easy' => 'легкий',
                'medium' => 'средний',
                'hard' => 'тяжелый',
            ];

            $this->merge([
                'difficulty' => $map[$normalized] ?? $normalized,
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'title' => 'required|string|max:255',
        'cooking_time' => 'required|integer|min:1',
        'difficulty' => 'required|string|max:50',
        'instructions' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        'tag_id' => 'nullable|exists:tags,id',

        // Валидация массивов ингредиентов
        'ingredients' => 'required|array|min:1', // Должен быть массив и минимум 1 элемент
        'ingredients.*' => 'required|exists:ingredients,id', // Каждый ID должен существовать в таблице ingredients
        
        'amounts' => 'required|array|min:1',
        'amounts.*' => 'required|numeric|min:0.1', // Количество должно быть числом больше нуля
        
        'units' => 'required|array|min:1',
        'units.*' => 'required|exists:units,id', // Каждый unit_id должен существовать в таблице units
             ];
    }
}
