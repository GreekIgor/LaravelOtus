@extends('layouts.app')

@section('title', isset($recipe) ? 'Редактирование: ' . $recipe->title : 'Создание рецепта')

@section('content')
<style>
    body { background-color: #f0f2f5; }
    .form-section {
        padding: 30px;
        margin-bottom: 25px;
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-top: 5px solid #0d6efd;
    }
    .ingredient-row .form-control, .ingredient-row .form-select { font-size: 0.9rem; }
</style>

@php
    $isEdit = isset($recipe) && $recipe->id;
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
                @if($isEdit) @method('PUT') @endif

                {{-- Основные данные --}}
                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-journal-text me-2"></i> Основные данные</h4>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Название рецепта</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               name="title" value="{{ old('title', $recipe->title ?? '') }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Время готовки (минут)</label>
                            <input type="number" class="form-control" name="cooking_time" 
                                   value="{{ old('cooking_time', $recipe->cooking_time ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Сложность</label>
                            <select class="form-select" name="difficulty" required>
                                @foreach(['легкий', 'средний', 'тяжелый'] as $level)
                                    <option value="{{ $level }}" {{ old('difficulty', $recipe->difficulty ?? '') == $level ? 'selected' : '' }}>
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
                    <input class="form-control" type="file" name="image">
                    @if($isEdit && isset($recipe->image_path))
                        <div class="mt-3">
                            <p class="small text-muted">Текущее изображение:</p>
                            <img src="{{ asset($recipe->image_path) }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
                </div>

                {{-- Ингредиенты --}}
                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-list-check me-2"></i> Ингредиенты</h4>
                    
                    <div id="ingredients-container">
                        {{-- Шаблон для JS (скрытый) --}}
                        <div class="row g-2 ingredient-row align-items-end mb-3 d-none" id="ingredient-template">
                            <div class="col-5">
                                <select class="form-select" name="ingredients[]">
                                    <option value="">Выберите ингредиент...</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" step="0.1" class="form-control" name="amounts[]" placeholder="Кол-во">
                            </div>
                            <div class="col-3">
                                <select class="form-select" name="units[]">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="removeIngredient(this.closest('.row'))">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Вывод существующих или пустой строки --}}
                        @php
                            $currentIngredients = old('ingredients', $isEdit ? $recipe->ingredients->pluck('id')->toArray() : []);
                            $currentAmounts = old('quantity', $isEdit ? $recipe->ingredients->pluck('pivot.quantity')->toArray() : []);
                        @endphp

                        @forelse($currentIngredients as $index => $currentId)
                            <div class="row g-2 ingredient-row align-items-end mb-3">
                                <div class="col-5">
                                    @if($loop->first)<label class="form-label fw-bold">Ингредиент</label>@endif
                                    <select class="form-select" name="ingredients[]" required>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" {{ $currentId == $ing->id ? 'selected' : '' }}>{{ $ing->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    @if($loop->first)<label class="form-label fw-bold">Кол-во</label>@endif
                                    <input type="number" step="0.1" class="form-control" name="amounts[]" value="{{ $currentAmounts[$index] ?? '' }}" required>
                                </div>
                                <div class="col-3">
                                    @if($loop->first)<label class="form-label fw-bold">Ед. изм.</label>@endif
                                    <select class="form-select" name="units[]">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (isset($recipe->ingredients[$index]) && $recipe->ingredients[$index]->pivot->unit_id == $unit->id) ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="removeIngredient(this.closest('.row'))">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            {{-- Если данных нет, показываем одну пустую строку выбора --}}
                            <div class="row g-2 ingredient-row align-items-end mb-3">
                                <div class="col-5">
                                    <label class="form-label fw-bold">Ингредиент</label>
                                    <select class="form-select" name="ingredients[]" required>
                                        <option value="">Выберите...</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-bold">Кол-во</label>
                                    <input type="number" step="0.1" class="form-control" name="amounts[]" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label fw-bold">Ед. изм.</label>
                                    <select class="form-select" name="units[]">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1"></div>
                            </div>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-outline-success mt-3" onclick="addIngredient()">
                        <i class="bi bi-plus-circle me-2"></i> Добавить ингредиент
                    </button>
                </div>

                {{-- Инструкция --}}
                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-book-half me-2"></i> Инструкция</h4>
                    <textarea class="form-control" name="instructions" rows="6" required>{{ old('instructions', $recipe->instructions ?? '') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('recipes.list') }}" class="btn btn-light btn-lg">Отмена</a>
                    <button type="submit" class="btn btn-primary btn-lg shadow">
                        <i class="bi bi-check-lg me-2"></i> {{ $isEdit ? 'Сохранить изменения' : 'Создать рецепт' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const template = document.getElementById('ingredient-template');
        const newRow = template.cloneNode(true);
        
        newRow.classList.remove('d-none');
        newRow.removeAttribute('id');
        
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        newRow.querySelectorAll('label').forEach(l => l.remove());
        
        container.appendChild(newRow);
    }

    function removeIngredient(row) {
        const rows = document.querySelectorAll('.ingredient-row:not(.d-none)');
        if (rows.length > 1) {
            row.remove();
        }
    }
</script>
@endsection