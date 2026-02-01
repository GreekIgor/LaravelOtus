@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary"><i class="bi bi-egg-fried me-2"></i> Управление ингредиентами</h1>
                <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                    <i class="bi bi-plus-circle me-2"></i> Добавить
                </button>
            </div>

            <div class="card shadow border-0 p-4">
                <table id="ingredientsTable" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Изображение</th>
                            <th>Наименование</th>
                            <th>Ед. изм.</th>
                            <th>Дата создания</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIngredientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addForm" action="{{ route('ingredients.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Добавить ингредиент</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Название</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Единица измерения</label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">Выберите...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Изображение</label>
                        <input type="file" name="img" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-success">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editIngredientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Редактировать ингредиент</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="editPreview" src="" class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Название</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Единица измерения</label>
                        <select name="unit_id" id="editUnitId" class="form-select" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Изменить изображение</label>
                        <input type="file" name="img" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="loader-overlay" style="display: none;">
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div>
</div>

<style>
#loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Затемнение */
    z-index: 9999; /* Поверх всех окон и модалок */
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {


// Показать лончер при начале любого AJAX-запроса
    let overlayTimeout;
    $(document).ajaxStart(function() {
        $('#loader-overlay').fadeIn(200);
        // Очистить предыдущий таймаут
        clearTimeout(overlayTimeout);
        // Автоматически скрыть overlay через 30 секунд (на случай зависших запросов)
        overlayTimeout = setTimeout(function() {
            $('#loader-overlay').fadeOut(200);
        }, 30000);
    });

    // Скрыть лончер после завершения запроса (даже если была ошибка)
    $(document).ajaxStop(function() {
        clearTimeout(overlayTimeout);
        $('#loader-overlay').fadeOut(200);
    });

    // Скрыть overlay при ошибке AJAX
    $(document).ajaxError(function() {
        clearTimeout(overlayTimeout);
        $('#loader-overlay').fadeOut(200);
    });

    // Скрыть overlay при завершении запроса (включая ошибки)
    $(document).ajaxComplete(function() {
        clearTimeout(overlayTimeout);
        $('#loader-overlay').fadeOut(200);
    });

    const table = $('#ingredientsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('ingredients.index', ['locale' => app()->getLocale()]) }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'img', 
                render: data => data 
                    ? `<img src="/storage/${data}" class="rounded" style="width:40px;height:40px;object-fit:cover;">`
                    : `<div class="bg-light rounded text-center" style="width:40px;height:40px;line-height:40px;"><i class="bi bi-image"></i></div>`
            },
            { data: 'name', name: 'name' },
            { data: 'unit_name', name: 'unit.name' }, 
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-end'
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ru.json' }
    });

    // ОБЩАЯ ФУНКЦИЯ ДЛЯ ОТПРАВКИ ФОРМ (Создание и Редактирование)
    function handleFormSubmit(formId, modalId) {
        $(formId).on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $(modalId).modal('hide');
                    $(formId)[0].reset();
                    table.ajax.reload(null, false);
                    Swal.fire('Успех!', response.message, 'success');
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON?.message || 'Ошибка при сохранении';
                    Swal.fire('Ошибка!', errorMsg, 'error');
                }
            });
        });
    }

    handleFormSubmit('#addForm', '#addIngredientModal');
    handleFormSubmit('#editForm', '#editIngredientModal');
});

// Глобальная функция для ручного скрытия overlay через консоль браузера (для отладки)
window.hideLoaderOverlay = function() {
    $('#loader-overlay').fadeOut(200);
    console.log('Loader overlay manually hidden');
};

// УДАЛЕНИЕ
function confirmDelete(url) {
    Swal.fire({
        title: 'Удалить ингредиент?',
        text: "Это действие нельзя отменить",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Да, удалить!',
        cancelButtonText: 'Отмена'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
                success: function(response) {
                    $('#ingredientsTable').DataTable().ajax.reload(null, false);
                    Swal.fire('Удалено!', response.message, 'success');
                }
            });
        }
    });
}

// РЕДАКТИРОВАНИЕ (Заполнение модалки)
function editIngredient(id, name, unitId, imgSrc) {
    const form = document.getElementById('editForm');
    form.action = `/admin/ingredients/${id}`;
    
    document.getElementById('editName').value = name;
    document.getElementById('editUnitId').value = unitId;
    
    const preview = document.getElementById('editPreview');
    if (imgSrc && imgSrc !== 'null') {
        preview.src = `/storage/${imgSrc}`;
        preview.style.display = 'inline-block';
    } else {
        preview.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('editIngredientModal')).show();
}
</script>
@endsection