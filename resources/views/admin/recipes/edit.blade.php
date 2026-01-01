@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4 text-primary">
                <i class="bi {{ $recipe->id ? 'bi-pencil-square' : 'bi-plus-circle' }} me-2"></i>
                {{ $recipe->id ? 'Редактирование: ' . $recipe->title : 'Создание нового рецепта' }}
            </h1>
            
            <form action="{{ $recipe->id ? route('recipes.update', $recipe->id) : route('recipes.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                
                @csrf
                @if($recipe->id) @method('PUT') @endif

                {{-- Основные данные --}}
                <div class="form-section shadow-sm p-4 bg-white rounded mb-4" style="border-top: 5px solid #0d6efd;">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-journal-text me-2"></i> Основные данные</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Название рецепта</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $recipe->title) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Время готовки (минут)</label>
                            <input type="number" class="form-control" name="time" value="{{ old('time', $recipe->time) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Сложность</label>
                            <select class="form-select" name="difficulty" required>
                                @foreach(['легкий', 'средний', 'тяжелый'] as $level)
                                    <option value="{{ $level }}" {{ old('difficulty', $recipe->difficulty) == $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Изображение --}}
                <div class="form-section shadow-sm p-4 bg-white rounded mb-4" style="border-top: 5px solid #0d6efd;">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-image me-2"></i> Изображение блюда</h4>
                    <div class="mb-3">
                        <input class="form-control" type="file" name="image" id="imageInput">
                    </div>
                    @if($recipe->img)
                        <div class="mt-3">
                            <p class="small text-muted">Текущее изображение:</p>
                            <img src="{{ asset('storage/' . $recipe->img) }}" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
                </div>

                {{-- Ингредиенты --}}
                <div class="form-section shadow-sm p-4 bg-white rounded mb-4" style="border-top: 5px solid #0d6efd;">
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
                                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.row').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Вывод существующих ингредиентов при редактировании --}}
                        @php
                            $currentIngredients = old('ingredients', $recipe->ingredients->pluck('id')->toArray() ?? []);
                            $currentAmounts = old('amounts', $recipe->ingredients->pluck('pivot.amount')->toArray() ?? []);
                        @endphp

                        @foreach($currentIngredients as $index => $currentId)
                            <div class="row g-2 ingredient-row align-items-end mb-3">
                                <div class="col-5">
                                    @if($loop->first)<label class="form-label fw-bold">Ингредиент</label>@endif
                                    <select class="form-select" name="ingredients[]" required>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" {{ $currentId == $ing->id ? 'selected' : '' }}>
                                                {{ $ing->name }}
                                            </option>
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
                                    <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.row').remove()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-success mt-3" onclick="addIngredient()">
                        <i class="bi bi-plus-circle me-2"></i> Добавить ингредиент
                    </button>
                </div>

                {{-- Инструкция --}}
                <div class="form-section shadow-sm p-4 bg-white rounded mb-4" style="border-top: 5px solid #0d6efd;">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-book-half me-2"></i> Инструкция</h4>
                    <textarea class="form-control" name="instructions" rows="6" required>{{ old('instructions', $recipe->instructions) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('recipes.index') }}" class="btn btn-light btn-lg">Отмена</a>
                    <button type="submit" class="btn btn-primary btn-lg shadow">
                        <i class="bi bi-check-lg me-2"></i> {{ $recipe->id ? 'Сохранить изменения' : 'Создать рецепт' }}
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
        
        // Очищаем поля в клоне
        newRow.querySelectorAll('input').forEach(i => i.value = '');
        newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        
        // Убираем лейблы в новых рядах
        newRow.querySelectorAll('label').forEach(l => l.remove());
        
        container.appendChild(newRow);
    }
</script>
@endsection