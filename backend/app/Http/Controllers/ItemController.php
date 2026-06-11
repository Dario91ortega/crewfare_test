<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * GET /api/items
     * Devuelve la lista completa de items en JSON.
     */
    public function index(): JsonResponse
    {
        return response()->json(Item::orderBy('id')->get());
    }

    /**
     * POST /api/items
     * Valida y crea un nuevo item.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $item = Item::create($data);

        return response()->json($item, 201);
    }

    /**
     * GET /api/items/{item}
     * Devuelve un item por id (404 automático si no existe).
     */
    public function show(Item $item): JsonResponse
    {
        return response()->json($item);
    }

    /**
     * PUT/PATCH /api/items/{item}
     * Actualiza un item existente.
     */
    public function update(Request $request, Item $item): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    /**
     * DELETE /api/items/{item}
     * Elimina un item.
     */
    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return response()->json(null, 204);
    }
}
