<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Рецепты</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #0d6efd;
            --bg-light: #f0f2f5;
            --sidebar-dark: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: var(--sidebar-dark);
            color: #fff;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
            text-align: center;
            display: flex; /* добавлено для выравнивания кнопки */
            justify-content: space-between;
            align-items: center;
        }

        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 4px 15px;
            transition: all 0.2s;
            text-decoration: none; /* убираем подчеркивание */
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            color: #fff;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .nav-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            margin: 20px 0 10px 30px;
            white-space: nowrap; /* чтобы текст не переносился при сжатии */
        }

        /* Main Content Styles */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: all 0.3s;
        }

        .top-navbar {
            background: #fff;
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .content-area {
            padding: 30px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            #sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            #main-wrapper { margin-left: 0; width: 100%; }
            #sidebar.active { margin-left: 0; }
        }

        /* Плавный переход для всех элементов */
        #sidebar, #main-wrapper, .nav-link, .logo-text, .nav-category {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Состояние свернутого меню */
        #sidebar.collapsed {
            width: 80px;
        }

        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .nav-category,
        #sidebar.collapsed .nav-link span {
            display: none; /* Скрываем текст */
        }

        #sidebar.collapsed .sidebar-header {
            justify-content: center !important;
        }

        #sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.5rem; /* Увеличиваем иконки в свернутом виде */
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            margin: 4px 10px;
        }

        /* Смещаем основной контент при сворачивании */
        #sidebar.collapsed + #main-wrapper {
            margin-left: 80px;
            width: calc(100% - 80px);
        }
    </style>
</head>
<body>

    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0 text-white fw-bold logo-text"><i class="bi bi-fire text-warning me-2"></i> ChefPanel</h4>
            <button class="btn text-white p-0 border-0" id="toggleSidebar">
                <i class="bi bi-layout-sidebar-inset-reverse fs-4"></i>
            </button>
        </div>

        <div class="nav-category">Главное</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> <span>Дашборд</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('recipes.index') }}" class="nav-link {{ request()->is('admin/recipes*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> <span>Рецепты</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('ingredients.index') }}" class="nav-link {{ request()->is('admin/ingredients*') ? 'active' : '' }}">
                    <i class="bi bi-egg-fried"></i> <span>Ингредиенты</span>
                </a>
            </li>
        </ul>

        <div class="nav-category">Управление</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-people"></i> <span>Пользователи</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-shield-lock"></i> <span>Роли</span>
                </a>
            </li>
        </ul>

        <div class="nav-category">Личное</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-person-circle"></i> <span>Профиль</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear"></i> <span>Настройки</span>
                </a>
            </li>
        </ul>

        <div class="mt-auto p-4">
            <form action="/logout" method="POST">
                @csrf
                <button class="btn btn-outline-danger w-100 btn-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right me-2"></i> <span class="logo-text">Выход</span>
                </button>
            </form>
        </div>
    </nav>

    <div id="main-wrapper">
        <header class="top-navbar">
            <button class="btn btn-light d-lg-none" id="sidebarCollapse">
                <i class="bi bi-list"></i>
            </button>
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Панель управления</li>
                </ol>
            </nav>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=0D6EFD&color=fff" alt="mdo" width="32" height="32" class="rounded-circle me-2">
                        <span class="d-none d-md-inline">Администратор</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Выход</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Сворачивание на десктопе
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        // Открытие на мобильных (ваша текущая логика)
        document.getElementById('sidebarCollapse')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>