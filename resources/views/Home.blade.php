@extends('layouts.app')

@section('title', 'Добро пожаловать')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center text-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold text-primary mb-4">Добро пожаловать в проект <br>"Книга Рецептов"!</h1>
            <p class="lead text-muted mb-5">
                Ищите, делитесь и открывайте для себя новые кулинарные шедевры.
                Ваше приключение в мире вкусов начинается здесь!
            </p>

            <div class="mb-5">
                <img src="{{ asset('kushen.webp') }}" 
                     class="img-fluid rounded shadow-lg" 
                     alt="Мультяшная кухня"
                     style="max-width: 100%; height: auto;">
            </div>
            
            <div class="card shadow-lg p-4 mb-5">
                <h3 class="card-title mb-4">Найти рецепт</h3>
                <form action="#" method="GET" class="d-flex flex-column flex-md-row">
                    <div class="input-group input-group-lg flex-grow-1 mb-3 mb-md-0 me-md-3">
                        <span class="input-group-text bg-white border-end-0" id="search-icon"><i class="bi bi-search"></i></span>
                        <input type="text" name="query" class="form-control border-start-0 ps-0" placeholder="Название рецепта, ингредиент..." aria-label="Поиск рецептов" aria-describedby="search-icon">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Искать рецепт</button>
                </form>
            </div>

            <p class="text-muted">
                Не нашли то, что искали? <a href="#" class="text-decoration-none">Предложите свой рецепт</a>!
            </p>
        </div>
    </div>
</div>
@endsection