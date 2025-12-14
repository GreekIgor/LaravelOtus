<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Премиум Рецепт: Салат "Цезарь" с курицей</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f0f2f5; /* Более мягкий светлый фон */
        }
        .recipe-header {
            /* Фоновое изображение (заглушка) */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1551608627-241517f6927d') center / cover;
            color: white;
            padding: 80px 0 50px;
            margin-bottom: 30px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        .recipe-image-wrapper {
            height: 450px; /* Увеличим высоту */
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3); /* Более сильная тень */
        }
        .recipe-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .ingredients-card {
            border-left: 5px solid #ffc107; /* Акцентная желтая полоса */
        }
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
            border-left: 5px solid #0d6efd; /* Синяя полоса для шагов */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .step-number {
            font-size: 2rem;
            font-weight: 700;
            color: #0d6efd;
            margin-right: 15px;
        }
    </style>
</head>
<body>

    <header class="recipe-header">
        <div class="container">
            <h1 class="display-2 fw-bolder text-center animate__animated animate__fadeInDown">Салат "Цезарь" c Идеальной Курицей</h1>
            <p class="lead text-center fs-5 mt-3">
                <i class="bi bi-star-fill text-warning me-2"></i> 
                Классика ресторанной кухни, которую легко повторить дома.
            </p>
        </div>
    </header>

    <div class="container">
        <div class="row gx-5">
            
            <div class="col-md-5 mb-4 order-md-2">
                
                <div class="card border-0 recipe-image-wrapper sticky-top" style="top: 20px;">
                    <img src="" alt="Салат Цезарь">
                    <div class="card-img-overlay d-flex align-items-end">
                        <div class="p-3 bg-dark bg-opacity-75 rounded-top">
                            <h5 class="mb-0">
                                <span class="badge bg-success me-2"><i class="bi bi-clock-fill"></i> 30 мин</span>
                                <span class="badge bg-info"><i class="bi bi-people-fill"></i> 4 Порции</span>
                                <span class="badge bg-danger"><i class="bi bi-speedometer"></i> Легкий</span>
                            </h5>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3"><i class="bi bi-info-circle-fill me-2"></i> Особенности</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-fire text-danger me-2"></i> <strong>Калорийность:</strong> 350 ккал / порция</li>
                            <li class="mb-2"><i class="bi bi-cup-hot-fill text-warning me-2"></i> <strong>Кухня:</strong> Итальянская/Американская</li>
                            <li class="mb-2"><i class="bi bi-calendar-event text-success me-2"></i> <strong>Лучшее время:</strong> Обед или легкий ужин</li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="col-md-7 mb-4 order-md-1">
                
                <div class="card ingredients-card mb-5 shadow-sm">
                    <div class="card-header bg-warning bg-opacity-10 text-dark fs-4 fw-bold">
                        <i class="bi bi-cart-fill me-2 text-warning"></i> Необходимые Продукты
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row row-cols-md-2 g-3">
                            
                            <div class="col">
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Куриное филе</span>
                                    <span class="fw-bold">300 г</span>
                                </div>
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Салат Романо</span>
                                    <span class="fw-bold">1 большой пучок</span>
                                </div>
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Помидоры Черри</span>
                                    <span class="fw-bold">10 шт.</span>
                                </div>
                            </div>
                            
                            <div class="col">
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Сыр Пармезан (тертый)</span>
                                    <span class="fw-bold">50 г</span>
                                </div>
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Соус Цезарь</span>
                                    <span class="fw-bold">По вкусу</span>
                                </div>
                                <div class="ingredient-item">
                                    <span class="text-secondary"><i class="bi bi-dot me-2"></i> Хлеб белый (для гренок)</span>
                                    <span class="fw-bold">2 ломтика</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-primary mb-4 border-bottom border-primary pb-2">
                        <i class="bi bi-bookmark-star-fill me-2"></i> Пошаговая Инструкция
                    </h2>
                    
                    <div class="instruction-step d-flex align-items-start">
                        <span class="step-number">1.</span>
                        <div>
                            <h4 class="fw-bold text-dark">Маринад и Жарка Курицы</h4>
                            <p class="text-muted">Филе натереть солью, перцем и каплей оливкового масла. Обжарить на сильном огне до золотистой корочки, затем довести до готовности в духовке или на медленном огне. Нарезать ломтиками.</p>
                        </div>
                    </div>
                    
                    <div class="instruction-step d-flex align-items-start">
                        <span class="step-number">2.</span>
                        <div>
                            <h4 class="fw-bold text-dark">Чесночные Кростини</h4>
                            <p class="text-muted">Хлеб порезать кубиками, сбрызнуть оливковым маслом и пропущенным через пресс чесноком. Запекать при 180°C до хрустящей текстуры. Остудить, чтобы сохранить хрусткость.</p>
                        </div>
                    </div>
                    
                    <div class="instruction-step d-flex align-items-start">
                        <span class="step-number">3.</span>
                        <div>
                            <h4 class="fw-bold text-dark">Сборка Салата</h4>
                            <p class="text-muted">Порвать листья Романо. Смешать их с половинами Черри. **Важно:** Соус добавляйте непосредственно перед подачей, чтобы листья не завяли.</p>
                        </div>
                    </div>
                    
                    <div class="instruction-step d-flex align-items-start">
                        <span class="step-number">4.</span>
                        <div>
                            <h4 class="fw-bold text-dark">Финальный Штрих</h4>
                            <p class="text-muted">Разложите салат по тарелкам, сверху выложите ломтики курицы, полейте соусом и обильно посыпьте свеженатертым Пармезаном. Наслаждайтесь!</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            crossorigin="anonymous"></script>
</body>
</html>