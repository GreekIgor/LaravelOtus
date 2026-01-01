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

            <div class="form-section p-4 overflow-hidden">
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



<script>
$(document).ready(function() {
    // Инициализация DataTable
    const table = $('#ingredientsTable').DataTable({
        processing: true,
        serverSide: true, // Включаем серверный поиск и пагинацию
        ajax: "{{ route('ingredients.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'img', 
                name: 'img',
                render: function(data) {
                    return data 
                        ? `<img src="/storage/${data}" class="rounded" style="width:40px;height:40px;object-fit:cover;">`
                        : `<div class="bg-light rounded text-center" style="width:40px;height:40px;line-height:40px;"><i class="bi bi-image"></i></div>`;
                }
            },
            { data: 'name', name: 'name' },
            { data: 'unit_name', name: 'unit.name' }, 
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                className: 'text-end'
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ru.json' // Руссификация
        }
    });
});

/**
 * Функция открытия модалки с предзаполнением
 */
function editIngredient(id, name, unitId, imgSrc) {
    const form = document.getElementById('editForm');
    form.action = `/admin/ingredients/${id}`;
    
    document.getElementById('editName').value = name;
    document.getElementById('editUnitId').value = unitId;
    
    const preview = document.getElementById('editPreview');
    if (imgSrc) {
        preview.src = `/storage/${imgSrc}`;
        preview.style.display = 'inline-block';
    } else {
        preview.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('editIngredientModal')).show();
}
</script>
@endsection