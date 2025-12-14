<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль Пользователя</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    
    <style>
        /* Дополнительные стили для центрирования и улучшения вида */
        body {
            background-color: #f8f9fa; /* Светлый фон */
        }
        .profile-card {
            margin-top: 50px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Мягкая тень */
            border-radius: 1rem;
        }
        .profile-img-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px; /* Центрируем и добавляем отступ снизу */
            border: 5px solid #0d6efd; /* Синяя рамка */
        }
        .profile-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <div class="card profile-card">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <div class="profile-img-container">
                                <img src="" alt="Фото пользователя">
                            </div>
                            
                            <h1 class="card-title mb-1">Иванов Иван Иванович</h1>
                            <p class="text-muted">Автор кулинарных шедевров</p>
                        </div>
                        
                        <hr class="my-4">

                        <div class="row">
                            
                            <div class="col-md-6 mb-3">
                                <h5 class="text-primary mb-3">Сведения об аккаунте</h5>
                                
                                <p class="mb-2">
                                    <strong><i class="bi bi-calendar-check me-2"></i> Дата регистрации:</strong>
                                    <span class="float-end">15.03.2023</span>
                                </p>
                                
                                <p class="mb-2">
                                    <strong><i class="bi bi-clock me-2"></i> Последний вход:</strong>
                                    <span class="float-end">01.12.2025, 22:10</span>
                                </p>
                            </div>
                            
                            <div class="col-md-6 mb-3 border-start">
                                <h5 class="text-primary mb-3">Активность</h5>
                                
                                <p class="mb-2">
                                    <strong><i class="bi bi-book me-2"></i> Рецептов опубликовано:</strong>
                                    <span class="float-end fs-5 fw-bold text-success">42</span>
                                </p>
                                
                                <p class="mb-2">
                                    <strong><i class="bi bi-star me-2"></i> Рейтинг автора:</strong>
                                    <span class="float-end">4.8 / 5.0</span>
                                </p>
                            </div>

                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                Сменить пароль
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Смена пароля</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="old-password" class="form-label">Текущий пароль</label>
                <input type="password" class="form-control" id="old-password">
              </div>
              <div class="mb-3">
                <label for="new-password" class="form-label">Новый пароль</label>
                <input type="password" class="form-control" id="new-password">
              </div>
              <div class="mb-3">
                <label for="confirm-password" class="form-label">Повторите новый пароль</label>
                <input type="password" class="form-control" id="confirm-password">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <button type="button" class="btn btn-primary">Сохранить пароль</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
