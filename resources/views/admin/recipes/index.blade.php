@extends('layouts.admin')

@section('content')

{{-- Скрытая форма для удаления --}}
<form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Список рецептов</h5>
            <a href="{{ route('recipes.create', ['locale' => app()->getLocale()]) }}" class="btn btn-sm btn-success">+ Создать</a>
        </div>
        <div class="card-body">
            <table id="serverRecipesTable" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Рецепт</th>
                        <th>Описание</th>
                        <th>Автор</th>
                        <th>Ингредиенты</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#serverRecipesTable').DataTable({
        processing: true,
        serverSide: true, // Ключевой параметр для работы через бэкенд
        ajax: {
            url: "{{ route('recipes.index', ['locale' => app()->getLocale()]) }}",
            type: "GET"
        },
        columns: [
            { data: 'title_info' },
            { data: 'description' },
            { data: 'author' },
            { data: 'ingredients_list' },
            { data: 'actions', orderable: false, className: 'text-end' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ru.json'
        }
    });
});

function confirmDelete(url) {
    if (confirm('Вы уверены, что хотите удалить этот рецепт?')) {
        let form = document.getElementById('delete-form');
        form.action = url;
        form.submit();
    }
}

</script>
@endsection