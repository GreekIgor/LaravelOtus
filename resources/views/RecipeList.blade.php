@extends('layouts.app')

@section('title', 'Главная страница')

@section('content')
<style>
    body { background-color: #f8f9fa; }
    .recipe-card { transition: transform 0.2s; height: 100%; display: flex; flex-direction: column; }
    .recipe-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; }
    .card-img-top { height: 200px; object-fit: cover; }
    .sticky-sidebar { position: sticky; top: 20px; }
    .badge-difficulty { padding: 5px 10px; font-size: 0.85em; font-weight: 600; border-radius: 4px; display: inline-block; }
    .difficulty-легкий { background-color: #d1e7dd; color: #0f5132; }
    .difficulty-средний { background-color: #fff3cd; color: #664d03; }
    .difficulty-тяжелый { background-color: #f8d7da; color: #842029; }

    .edit-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.9);
        color: #0d6efd;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .edit-badge:hover {
        background-color: #0d6efd;
        color: white;
        transform: scale(1.1);
    }
    /* Контейнер для относительного позиционирования иконки */
    .recipe-card { position: relative; }
</style>

<div class="container py-5">
    <h1 class="mb-5 text-center fw-bold">Кулинарная Книга</h1>

    <div class="row">
        <div class="col-lg-3">
            <form action="{{ route('recipes.list') }}" method="GET">
                <div class="card p-3 sticky-sidebar shadow-sm border-0">
                    <h5 class="card-title mb-3"><i class="bi bi-funnel-fill me-2 text-primary"></i> Фильтры</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Название</label>
                        <input type="text" name="name" class="form-control" value="{{ request('name') }}" placeholder="Найти рецепт...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Ингредиенты</label>
                        <input type="text" name="ingredients" class="form-control" value="{{ request('ingredients') }}" placeholder="Например: сыр, мясо">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Сложность</label>
                        @foreach(['легкий', 'средний', 'тяжелый'] as $level)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="difficulty[]" value="{{ $level }}" 
                                id="diff-{{ $level }}" {{ in_array($level, (array)request('difficulty')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="diff-{{ $level }}">{{ ucfirst($level) }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Время готовки</label>
                        <select name="time" class="form-select">
                            <option value="">Любое время</option>
                            <option value="быстро" {{ request('time') == 'быстро' ? 'selected' : '' }}>До 30 мин</option>
                            <option value="средне" {{ request('time') == 'средне' ? 'selected' : '' }}>30-60 мин</option>
                            <option value="долго" {{ request('time') == 'долго' ? 'selected' : '' }}>Более 60 мин</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Применить</button>
                    <a href="{{ route('recipes.list') }}" class="btn btn-link btn-sm w-100 mt-2 text-decoration-none text-muted">Сбросить всё</a>
                </div>
            </form>
        </div>

        <div class="col-lg-9">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @forelse($recipes as $recipe)
                <div class="col">
                    <div class="card recipe-card shadow-sm border-0">
                        @can('update', $recipe)
                            <a href="{{ route('recipe-edit' , $recipe->id) }}" class="edit-badge" title="Редактировать">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        @endcan
                        <img src="{{ $recipe->image_path ? asset($recipe->image_path) : asset('default.jpg') }}" 
                             class="card-img-top" alt="{{ $recipe->title }}">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $recipe->title }}</h5>
                            <div class="badge-difficulty difficulty-{{ $recipe->difficulty }} mb-2 text-capitalize">
                                {{ $recipe->difficulty }}
                            </div>
                            <p class="card-text small text-muted">
                                <i class="bi bi-clock me-1"></i> {{ $recipe->cooking_time }} мин.
                                <br>
                                <i class="bi bi-egg-fried me-1"></i> 
                                {{ $recipe->ingredients->pluck('name')->take(3)->implode(', ') }}...
                            </p>
                            <a href="{{ route('recipe.detail', $recipe->id) }}" class="btn btn-sm btn-outline-primary mt-auto w-100">Открыть рецепт</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">Ничего не найдено :(</h3>
                    <p>Попробуйте изменить параметры поиска</p>
                </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $recipes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection