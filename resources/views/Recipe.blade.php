@extends('layouts.app')

@section('title', $recipe->title)

@section('content')
    <style>
        body { background-color: #f0f2f5; }
        .recipe-header {
            /* Используем динамическое изображение для фона заголовка или оставляем заглушку */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), 
                        url('{{ asset('storage/' . $recipe->image_path) }}') center / cover;
            color: white;
            padding: 80px 0 50px;
            margin-bottom: 30px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        .recipe-image-wrapper {
            height: 450px;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
        }
        .recipe-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .ingredients-card { border-left: 5px solid #ffc107; }
        .ingredient-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dotted #e9ecef;
        }
        .instruction-step {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 0.75rem;
            border-left: 5px solid #0d6efd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .step-number {
            font-size: 2rem;
            font-weight: 700;
            color: #0d6efd;
            margin-right: 15px;
        }
    </style>

    <header class="recipe-header">
        <div class="container">
            <h1 class="display-2 fw-bolder text-center">{{ $recipe->title }}</h1>
            <p class="lead text-center fs-5 mt-3">
                <i class="bi bi-star-fill text-warning me-2"></i> 
                {{ $recipe->description }}
            </p>
        </div>
    </header>

    <div class="container">
        <div class="row gx-5">
            
            {{-- Правая колонка (Инфо и Фото) --}}
            <div class="col-md-5 mb-4 order-md-2">
                <div class="card border-0 recipe-image-wrapper sticky-top" style="top: 20px;">
                    <img src="{{ asset( $recipe->image_path) }}" alt="{{ $recipe->title }}">
                    <div class="card-img-overlay d-flex align-items-end">
                        <div class="p-3 bg-dark bg-opacity-75 rounded-top w-100">
                            <h5 class="mb-0 text-center">
                                <span class="badge bg-success me-2"><i class="bi bi-clock-fill"></i> {{ $recipe->cooking_time }} мин</span>
                                <span class="badge bg-danger"><i class="bi bi-speedometer"></i> {{ ucfirst($recipe->difficulty) }}</span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Левая колонка (Ингредиенты и Инструкции) --}}
            <div class="col-md-7 mb-4 order-md-1">
                
                {{-- Карточка ингредиентов --}}
                <div class="card ingredients-card mb-5 shadow-sm">
                    <div class="card-header bg-warning bg-opacity-10 text-dark fs-4 fw-bold">
                        <i class="bi bi-cart-fill me-2 text-warning"></i> Ингредиенты
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-cols-md-2 g-3">
                            @foreach($recipe->ingredients as $ingredient)
                                <div class="col">
                                    <div class="ingredient-item">
                                        <span class="text-secondary"><i class="bi bi-dot me-2"></i> {{ $ingredient->name }}</span>
                                        <span class="fw-bold">{{ $ingredient->pivot->quantity }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                {{-- Пошаговая инструкция --}}
                <div>
                    <h2 class="text-primary mb-4 border-bottom border-primary pb-2">
                        <i class="bi bi-bookmark-star-fill me-2"></i> Инструкция по приготовлению
                    </h2>
                    
                    {{-- Разбиваем текст инструкции по строкам --}}
                    @php 
                        $steps = explode("\n", $recipe->instructions); 
                    @endphp

                    @foreach($steps as $index => $step)
                        @if(trim($step))
                            <div class="instruction-step d-flex align-items-start">
                                <span class="step-number">{{ $index + 1 }}.</span>
                                <div>
                                    <p class="text-dark fs-5">{{ $step }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>

@endsection