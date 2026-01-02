@extends('layouts.app')

@section('title', $recipe->title)

@section('content')

    <style>

        body {

            background-color: #f0f2f5;

        }

        .form-section {

            padding: 30px;

            margin-bottom: 25px;

            background-color: #fff;

            border-radius: 0.75rem;

            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);

            border-top: 5px solid #0d6efd; /* Акцентная полоса */

        }

        .ingredient-row .form-control, .ingredient-row .form-select {

            font-size: 0.9rem;

        }

        .remove-ingredient-btn {

            height: 100%; /* Убедимся, что кнопка занимает всю высоту */

        }

    </style>
@php
    $isEdit = isset($recipe) && $recipe->id;
    // Определяем маршрут: если редактируем - update, если создаем - store
    $route = $isEdit ? route('recipe-edit', $recipe->id) : route('recipe-store');
@endphp

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-10">

<h1 class="mb-4 text-primary">
    <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' }} me-2"></i> 
    {{ $isEdit ? 'Редактирование: ' . $recipe->title : 'Создание нового рецепта' }}
</h1>

<form action="{{ $route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Основные данные --}}
    <div class="form-section">
        <h4 class="mb-4 text-secondary"><i class="bi bi-journal-text me-2"></i> Основные данные</h4>
        <div class="mb-3">
            <label for="recipeTitle" class="form-label fw-bold">Название рецепта</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                   id="recipeTitle" name="title" 
                   value="{{ old('title', $recipe->title ?? '') }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cookingTime" class="form-label fw-bold">Время готовки (минут)</label>
                <input type="number" class="form-control" id="cookingTime" name="cooking_time" 
                       value="{{ old('cooking_time', $recipe->cooking_time ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="difficulty" class="form-label fw-bold">Сложность</label>
                <select class="form-select" id="difficulty" name="difficulty" required>
                    @foreach(['легкий', 'средний', 'тяжелый'] as $level)
                        <option value="{{ $level }}" {{ (old('difficulty', $recipe->difficulty ?? '') == $level) ? 'selected' : '' }}>
                            {{ ucfirst($level) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Изображение --}}
    <div class="form-section">
        <h4 class="mb-4 text-secondary"><i class="bi bi-image me-2"></i> Изображение блюда</h4>
        <div class="mb-3">
            <label for="recipeImage" class="form-label fw-bold">
                {{ $isEdit ? 'Загрузить новое изображение' : 'Загрузить изображение' }}
            </label>
            <input class="form-control" type="file" id="recipeImage" name="image">
        </div>

        @if($isEdit && isset($recipe->image_path))
            <p class="mt-3">Текущее изображение:</p>
            <div class="d-flex align-items-start">
                <img src="{{ asset($recipe->image_path) }}" alt="Текущее изображение" class="img-thumbnail" style="max-height: 200px;">
            </div>
        @endif
    </div>

    {{-- Ингредиенты --}}
    <div class="form-section">
        <h4 class="mb-4 text-secondary"><i class="bi bi-list-check me-2"></i> Ингредиенты</h4>
        
        <div id="ingredients-container">
            {{-- Скрытый шаблон для JS --}}
            <div class="row g-2 ingredient-row align-items-end mb-3" id="ingredient-template" style="display: none;">
                <div class="col-5">
                    <input type="text" class="form-control" name="ingredients_new_name[]" placeholder="Наименование">
                </div>
                <div class="col-3">
                    <input type="number" class="form-control" name="ingredients_new_amount[]" placeholder="Кол-во">
                </div>
                <div class="col-3">
                    <select class="form-select" name="ingredients_new_unit[]">
                        <option value="г">г</option>
                        <option value="мл">мл</option>
                        <option value="шт">шт</option>
                        <option value="ст.л.">ст.л.</option>
                    </select>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger remove-ingredient-btn" onclick="removeIngredient(this.parentNode.parentNode)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Если мы редактируем, выводим текущие ингредиенты --}}
            @if($isEdit && $recipe->ingredients->count() > 0)
                @foreach($recipe->ingredients as $index => $ingredient)
                <div class="row g-2 ingredient-row align-items-end mb-3">
                    <div class="col-5">
                        @if($index === 0) <label class="form-label small">Наименование</label> @endif
                        <input type="text" class="form-control" name="ingredients_existing[{{ $ingredient->id }}][name]" value="{{ $ingredient->name }}" required>
                    </div>
                    <div class="col-3">
                        @if($index === 0) <label class="form-label small">Кол-во</label> @endif
                        <input type="number" class="form-control" name="ingredients_existing[{{ $ingredient->id }}][amount]" value="{{ $ingredient->pivot->amount }}" required>
                    </div>
                    <div class="col-3">
                        @if($index === 0) <label class="form-label small">Ед. изм.</label> @endif
                        <select class="form-select" name="ingredients_existing[{{ $ingredient->id }}][unit]">
                            @foreach(['г', 'мл', 'шт', 'ст.л.'] as $unit)
                                <option value="{{ $unit }}" {{ $ingredient->pivot->unit == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger remove-ingredient-btn" onclick="removeIngredient(this.parentNode.parentNode)">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Если это создание, выводим одну пустую строку сразу --}}
                <div class="row g-2 ingredient-row align-items-end mb-3">
                    <div class="col-5">
                        <label class="form-label small">Наименование</label>
                        <input type="text" class="form-control" name="ingredients_new_name[]" required>
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Кол-во</label>
                        <input type="number" class="form-control" name="ingredients_new_amount[]" required>
                    </div>
                    <div class="col-3">
                        <label class="form-label small">Ед. изм.</label>
                        <select class="form-select" name="ingredients_new_unit[]">
                            <option value="г">г</option>
                            <option value="мл">мл</option>
                            <option value="шт">шт</option>
                            <option value="ст.л.">ст.л.</option>
                        </select>
                    </div>
                    <div class="col-1">
                         {{-- Первую строку при создании обычно не удаляют --}}
                    </div>
                </div>
            @endif
        </div>
        
        <div class="d-grid mt-4">
            <button type="button" class="btn btn-outline-success" onclick="addIngredient()">
                <i class="bi bi-plus-circle me-2"></i> Добавить ингредиент
            </button>
        </div>
    </div>

    {{-- Инструкция --}}
    <div class="form-section">
        <h4 class="mb-4 text-secondary"><i class="bi bi-book-half me-2"></i> Инструкция</h4>
        <div class="mb-3">
            <textarea class="form-control" name="description" rows="8" required placeholder="Напишите шаги приготовления...">{{ old('description', $recipe->description ?? '') }}</textarea>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('recipes.list') }}" class="btn btn-secondary btn-lg">Отменить</a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-2"></i> 
            {{ $isEdit ? 'Сохранить изменения' : 'Создать рецепт' }}
        </button>
    </div>
</form>
        </div>

    </div> 
    <script>

    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const template = document.getElementById('ingredient-template');


        const newRow = template.cloneNode(true);
        
        newRow.removeAttribute('id');
        newRow.style.display = 'flex'; 
        

        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
            input.required = true; 
        });

        newRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });


        container.appendChild(newRow);
        newRow.querySelectorAll('label').forEach(label => {
            label.style.display = 'none';
        });
    }


    function removeIngredient(rowElement) {
        const rows = document.querySelectorAll('.ingredient-row:not(#ingredient-template)');
        if (rows.length > 1) {
            rowElement.remove();
        } else {
            alert('В рецепте должен быть хотя бы один ингредиент');
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {

    });
</script> 
@endsection