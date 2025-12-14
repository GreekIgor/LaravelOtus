<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список Рецептов</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr-com/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .recipe-card {
            transition: transform 0.2s;
            height: 100%; /* Гарантируем одинаковую высоту карточек */
        }
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        /* Стиль для Sticky Sidebar (фильтров) */
        .sticky-sidebar {
            position: sticky;
            top: 20px; /* Отступ от верхнего края при прокрутке */
        }
        .badge-difficulty {
            padding: 5px 10px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .difficulty-легкий { background-color: #d1e7dd; color: #0f5132; }
        .difficulty-средний { background-color: #fff3cd; color: #664d03; }
        .difficulty-тяжелый { background-color: #f8d7da; color: #842029; }
    </style>
</head>
<body>

<div class="container-fluid py-5">
    <div class="container">
        <h1 class="mb-4 text-center">Кулинарная Книга</h1>

        <div class="row">
            
            <div class="col-lg-3">
                <div class="card p-3 sticky-sidebar shadow-sm">
                    <h5 class="card-title mb-3"><i class="bi bi-funnel-fill me-2"></i> Фильтры</h5>
                    
                    <div class="mb-3">
                        <label for="filter-name" class="form-label fw-bold">Название рецепта</label>
                        <input type="text" class="form-control" id="filter-name" placeholder="Найти...">
                    </div>

                    <div class="mb-3">
                        <label for="filter-ingredients" class="form-label fw-bold">Ингредиенты (поиск по составу)</label>
                        <input type="text" class="form-control" id="filter-ingredients" placeholder="Например: курица, томаты">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Сложность</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="легкий" id="difficulty-light">
                            <label class="form-check-label" for="difficulty-light">
                                Легкий
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="средний" id="difficulty-medium">
                            <label class="form-check-label" for="difficulty-medium">
                                Средний
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="тяжелый" id="difficulty-hard">
                            <label class="form-check-label" for="difficulty-hard">
                                Тяжелый
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Сортировка по времени готовки</label>
                        <select class="form-select" aria-label="Сортировка по времени">
                            <option selected>По умолчанию</option>
                            <option value="быстро">Готовится быстро (до 30 мин)</option>
                            <option value="средне">Готовится средне (30-60 мин)</option>
                            <option value="долго">Готовится долго (более 60 мин)</option>
                        </select>
                    </div>

                    <button class="btn btn-primary mt-2">Применить фильтры</button>
                    <button class="btn btn-outline-secondary mt-2">Сбросить</button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    
                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="" class="card-img-top" alt="Салат Цезарь">
                            <div class="card-body">
                                <h5 class="card-title">Салат "Цезарь" с курицей</h5>
                                <div class="badge-difficulty difficulty-легкий mb-2">Легкий</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 25 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Курица, Романо, Гренки, Пармезан
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="

[Image of traditional Ukrainian borscht soup]
" class="card-img-top" alt="Борщ">
                            <div class="card-body">
                                <h5 class="card-title">Украинский Борщ</h5>
                                <div class="badge-difficulty difficulty-тяжелый mb-2">Тяжелый</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 90 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Свекла, Капуста, Мясо, Картофель
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="" class="card-img-top" alt="Панкейки">
                            <div class="card-body">
                                <h5 class="card-title">Пышные Американские Панкейки</h5>
                                <div class="badge-difficulty difficulty-легкий mb-2">Легкий</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 15 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Мука, Яйца, Молоко, Разрыхлитель
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="" class="card-img-top" alt="Лазанья">
                            <div class="card-body">
                                <h5 class="card-title">Домашняя Лазанья Болоньезе</h5>
                                <div class="badge-difficulty difficulty-средний mb-2">Средний</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 70 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Фарш, Листы пасты, Соус Бешамель, Сыр
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="

[Image of traditional Hungarian beef goulash stew]
" class="card-img-top" alt="Гуляш">
                            <div class="card-body">
                                <h5 class="card-title">Венгерский Говяжий Гуляш</h5>
                                <div class="badge-difficulty difficulty-тяжелый mb-2">Тяжелый</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 120 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Говядина, Паприка, Лук, Томатная паста
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="card recipe-card shadow-sm">
                            <img src="" class="card-img-top" alt="Смузи">
                            <div class="card-body">
                                <h5 class="card-title">Зеленый Смузи с Бананом</h5>
                                <div class="badge-difficulty difficulty-легкий mb-2">Легкий</div>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-clock me-1"></i> 5 минут 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-list-check me-1"></i> Шпинат, Банан, Йогурт, Мед
                                </p>
                                <a href="#" class="btn btn-sm btn-outline-primary mt-2">Смотреть рецепт</a>
                            </div>
                        </div>
                    </div>
                    
                    </div>
                
                <nav aria-label="Навигация по рецептам" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Предыдущая</a>
                        </li>
                        <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Следующая</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>
</body>
</html>