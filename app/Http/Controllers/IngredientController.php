<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngredientRequest;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\IngredientService;

class IngredientController extends Controller
{
    protected $ingredientService;

    public function __construct (IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        $query = Ingredient::with('unit')->select('ingredients.*');

        return datatables()->of($query)
            ->addColumn('unit_name', function($row) {
                return $row->unit->name ?? '-';
            })
            // Форматируем дату прямо на бэкенде
            ->editColumn('created_at', function($row) {
                return $row->created_at ? $row->created_at->format('d.m.Y H:i') : '-';
            })
            ->addColumn('actions', function($row) {
                return '
                    <button class="btn btn-outline-primary btn-sm" 
                            onclick="editIngredient('.$row->id.', \''.$row->name.'\', '.$row->unit_id.', \''.$row->img.'\')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="'.route('ingredients.destroy', $row->id).'" method="POST" class="d-inline">
                        '.csrf_field().method_field('DELETE').'
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Удалить?\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>';
            })
            ->rawColumns(['actions']) // Чтобы HTML в кнопках не экранировался
            ->make(true);
    }

    $units = Unit::all();
    return view('admin.ingredients.index', compact('units'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientRequest $request)
    {
        $this->ingredientService->createIngredient($request->validated());
        return redirect()->route('ingredients.index')->with('success', 'Ingredient created successfully.'   );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $ingredient = $this->ingredientService->getIngredientById($id);
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IngredientRequest $request, string $id)
    {
        $this->ingredientService->updateIngredient($id, $request->validated());
        return redirect()->route('ingredients.index')->with('success', 'Ingredient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->ingredientService->deleteIngredient($id);
        return redirect()->route('ingredients.index')->with('success', 'Ingredient deleted successfully.'); 
    }
}
