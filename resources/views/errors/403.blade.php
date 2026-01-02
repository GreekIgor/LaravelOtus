@extends('layouts.app')

@section('title', 'Доступ ограничен')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        {{-- Большая иконка --}}
        <div class="mb-4">
            <i class="bi bi-shield-lock-fill text-danger" style="font-size: 6rem;"></i>
        </div>

        {{-- Заголовок ошибки --}}
        <h1 class="display-1 fw-bold text-dark">403</h1>
        <h2 class="mb-4">Упс! Доступ ограничен</h2>

        {{-- Текст сообщения --}}
        <p class="lead text-muted mb-5">
            Извините, но у вашей роли (<strong>{{ auth()->user()->role ?? 'Гость' }}</strong>) 
            недостаточно прав для просмотра этой страницы.
        </p>

        {{-- Панель с кнопками (Тот самый Header Panel) --}}
        <div class="card shadow-sm border-0 bg-white p-4">
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-house-door me-2"></i> Вернуться на главную
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-arrow-left me-2"></i> Назад
                </a>
            </div>
        </div>
    </div>
</div>
@endsection