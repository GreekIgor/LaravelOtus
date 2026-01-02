<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <i class="bi bi-fire text-warning me-2"></i>
            <span>Chef<span class="text-primary">Panel</span></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('recipes.list') }}">Все рецепты</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Категории</a></li>
                @can('create', App\Models\Recipe::class)
                <li class="nav-item"><a class="nav-link" href="{{ route('recipe-create') }}">Добавить рецепт</a></li>
                @endcan
            </ul>

            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn btn-link link-dark text-decoration-none dropdown-toggle p-0" data-bs-toggle="dropdown">
                        <i class="bi bi-translate me-1"></i> RU
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#">Русский</a></li>
                        <li><a class="dropdown-item" href="#">English</a></li>
                        <li><a class="dropdown-item" href="#">Қазақша</a></li>
                    </ul>
                </div>

                @auth
                    <div class="dropdown">
                                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D6EFD&color=fff" 
                                width="38" height="38" class="rounded-circle me-2">
                            
                            <div class="d-flex flex-column">
                                <span class="fw-bold lh-1">{{ Auth::user()->name }}</span>
                                <span class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    {{-- Переводим роль на русский для красоты (опционально) --}}
                                    @if(Auth::user()->isAdmin()) Администратор 
                                    @elseif(Auth::user()->isModerator()) Модератор 
                                    @else Пользователь 
                                    @endif
                                </span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Аккаунт</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}#change-password"><i class="bi bi-shield-lock me-2"></i>Сменить пароль</a></li>
                            @if(Auth::user()->is_admin) {{-- Если есть флаг админа --}}
                                <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Админка</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="app-logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Выход
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Вход</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                @endauth
            </div>
        </div>
    </div>
</nav>