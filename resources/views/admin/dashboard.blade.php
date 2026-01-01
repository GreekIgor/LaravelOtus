@extends('layouts.admin')

@section('title', 'Дашборд')
@section('breadcrumb', 'Панель управления')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Пользователи</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['users_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-journal-text text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Рецепты</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['recipes_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-egg-fried text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Ингредиенты</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['ingredients_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0 text-dark">Динамика регистраций (7 дней)</h6>
                </div>
                <div class="card-body">
                    <canvas id="regChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0 text-dark">Топ 10 авторов</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted">
                                    <th class="ps-3">Автор</th>
                                    <th class="text-end pe-3">Рецепты</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topAuthors as $author)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="small fw-medium">{{ $author->name }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                            {{ $author->recipes_count }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('regChart').getContext('2d');
    
    // Подготовка данных из PHP
    const labels = {!! json_encode($registrations->pluck('date')) !!};
    const dataValues = {!! json_encode($registrations->pluck('count')) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Новые пользователи',
                data: dataValues,
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                borderColor: '#0d6efd',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush