<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngredientRequest;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\IngredientService;
use Illuminate\Support\Facades\Gate;

class IngredientController extends Controller
{
    protected $ingredientService;

    public function __construct(IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }

    public function index(Request $request)
    {
        // Используем разрешение manage-ingredients вместо проверки роли
        Gate::authorize('manage-ingredients');

        if ($request->ajax()) {
            $query = Ingredient::with('unit')->select('ingredients.*');

            return datatables()->of($query)
                ->addColumn('unit_name', function($row) {
                    return $row->unit->name ?? '-';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at ? $row->created_at->format('d.m.Y H:i') : '-';
                })
                ->addColumn('actions', function($row) {
                    // Используем функцию confirmDelete (через SweetAlert2), которую мы писали ранее
                    return '
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm" 
                                    onclick="editIngredient('.$row->id.', \''.$row->name.'\', '.$row->unit_id.', \''.$row->img.'\')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="confirmDelete(\''.route('ingredients.destroy', $row->id).'\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $units = Unit::all();
        return view('admin.ingredients.index', compact('units'));
    }

    public function store(IngredientRequest $request)
    {
        Gate::authorize('manage-ingredients');
        $ingredient = $this->ingredientService->createIngredient($request->validated());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ингредиент создан!'
            ]);
        }

        $locale = app()->getLocale();
        return redirect()->route('ingredients.index', ['locale' => $locale])->with('success', 'Создано успешно');
    }

    public function update(IngredientRequest $request, string $id)
    {
        Gate::authorize('manage-ingredients');
        $this->ingredientService->updateIngredient($id, $request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ингредиент обновлен!'
            ]);
        }

        $locale = app()->getLocale();
        return redirect()->route('ingredients.index', ['locale' => $locale])->with('success', 'Обновлено успешно');
    }

    public function destroy(string $id)
    {
        Gate::authorize('manage-ingredients');
        $this->ingredientService->deleteIngredient($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ингредиент удален'
            ]);
        }

        $locale = app()->getLocale();
        return redirect()->route('ingredients.index', ['locale' => $locale])->with('success', 'Удалено успешно'); 
    }
}