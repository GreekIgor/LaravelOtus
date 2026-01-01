<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование Рецепта</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr-com/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i> Редактирование Рецепта: "Салат Цезарь"</h1>
            
            <form action="#" method="POST" enctype="multipart/form-data">
                
                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-journal-text me-2"></i> Основные данные</h4>

                    <div class="mb-3">
                        <label for="recipeTitle" class="form-label fw-bold">Название рецепта</label>
                        <input type="text" class="form-control" id="recipeTitle" name="title" value="Салат &quot;Цезарь&quot; с курицей" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cookingTime" class="form-label fw-bold">Время готовки (минут)</label>
                            <input type="number" class="form-control" id="cookingTime" name="time" value="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="difficulty" class="form-label fw-bold">Сложность</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="легкий" selected>Легкий</option>
                                <option value="средний">Средний</option>
                                <option value="тяжелый">Тяжелый</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-image me-2"></i> Изображение блюда</h4>
                    
                    <div class="mb-3">
                        <label for="recipeImage" class="form-label fw-bold">Загрузить новое изображение</label>
                        <input class="form-control" type="file" id="recipeImage" name="image">
                    </div>

                    <p class="mt-3">Текущее изображение:</p>
                    <img src="" alt="Текущее изображение" class="img-thumbnail" style="max-height: 200px;">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-3"><i class="bi bi-trash"></i> Удалить</button>
                </div>

                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-list-check me-2"></i> Ингредиенты</h4>
                    
                    <div id="ingredients-container">
                        
                        <div class="row g-2 ingredient-row align-items-end mb-3" id="ingredient-template" style="display: none;">
                            <div class="col-5">
                                <label class="form-label">Наименование</label>
                                <input type="text" class="form-control" name="ingredient_name[]" placeholder="Куриное филе">
                            </div>
                            <div class="col-3">
                                <label class="form-label">Кол-во/Вес</label>
                                <input type="number" class="form-control" name="ingredient_amount[]" value="300">
                            </div>
                            <div class="col-3">
                                <label class="form-label">Ед. измерения</label>
                                <select class="form-select" name="ingredient_unit[]">
                                    <option value="г">г</option>
                                    <option value="мл">мл</option>
                                    <option value="шт" selected>шт</option>
                                    <option value="ст.л.">ст.л.</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger remove-ingredient-btn d-flex align-items-center justify-content-center" onclick="removeIngredient(this.parentNode.parentNode)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-2 ingredient-row align-items-end mb-3">
                            <div class="col-5">
                                <label class="form-label">Наименование</label>
                                <input type="text" class="form-control" name="ingredient_name[]" value="Куриное филе" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Кол-во/Вес</label>
                                <input type="number" class="form-control" name="ingredient_amount[]" value="300" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Ед. измерения</label>
                                <select class="form-select" name="ingredient_unit[]">
                                    <option value="г" selected>г</option>
                                    <option value="мл">мл</option>
                                    <option value="шт">шт</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger remove-ingredient-btn d-flex align-items-center justify-content-center" onclick="removeIngredient(this.parentNode.parentNode)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-2 ingredient-row align-items-end mb-3">
                            <div class="col-5">
                                <label class="form-label">Наименование</label>
                                <input type="text" class="form-control" name="ingredient_name[]" value="Салат Романо" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Кол-во/Вес</label>
                                <input type="number" class="form-control" name="ingredient_amount[]" value="1" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Ед. измерения</label>
                                <select class="form-select" name="ingredient_unit[]">
                                    <option value="г">г</option>
                                    <option value="мл">мл</option>
                                    <option value="шт" selected>шт</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger remove-ingredient-btn d-flex align-items-center justify-content-center" onclick="removeIngredient(this.parentNode.parentNode)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-outline-success" onclick="addIngredient()">
                            <i class="bi bi-plus-circle me-2"></i> Добавить ингредиент
                        </button>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="mb-4 text-secondary"><i class="bi bi-book-half me-2"></i> Инструкция по приготовлению</h4>
                    
                    <div class="mb-3">
                        <label for="instructions" class="form-label fw-bold">Пошаговое описание</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="8" required>1. Филе натереть солью, перцем и каплей оливкового масла. Обжарить до готовности.
2. Хлеб порезать кубиками, сбрызнуть оливковым маслом и чесноком. Запекать до хрустящей текстуры.
3. Листья Романо порвать руками. Смешать их с половинами Черри.
4. Разложите салат по тарелкам, сверху выложите ломтики курицы, полейте соусом и посыпьте Пармезаном.</textarea>
                    </div>

                    </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="recipe_page_fancy.html" class="btn btn-secondary btn-lg">Отменить</a>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-2"></i> Сохранить изменения</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        crossorigin="anonymous"></script>

<script>
    /**
     * Динамически добавляет новую строку для ингредиента на форму.
     */
    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const template = document.getElementById('ingredient-template');

        // Клонируем шаблон
        const newRow = template.cloneNode(true);
        
        // Удаляем id шаблона и делаем блок видимым
        newRow.removeAttribute('id');
        newRow.style.display = 'flex'; // Делаем row видимым
        
        // Очищаем поля (для чистого добавления)
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.value = 'г'); // Устанавливаем значение по умолчанию

        // Добавляем новый ряд в контейнер
        container.appendChild(newRow);

        // Убираем лейблы в новом ряду, так как они уже есть в первом ряду
        newRow.querySelectorAll('label').forEach(label => label.style.display = 'none');
    }

    /**
     * Удаляет строку ингредиента.
     */
    function removeIngredient(rowElement) {
        rowElement.remove();
    }
    
    // При загрузке страницы удаляем лейблы у всех существующих строк, кроме первой
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('#ingredients-container .ingredient-row');
        if (rows.length > 0) {
            // Скрываем лейблы у всех строк, кроме первой
            rows.forEach((row, index) => {
                if (index > 0) {
                    row.querySelectorAll('label').forEach(label => label.style.display = 'none');
                }
            });
        }
    });

    // Убираем лейблы из скрытого шаблона
    document.getElementById('ingredient-template').querySelectorAll('label').forEach(label => label.style.display = 'none');

</script>
</body>
</html>