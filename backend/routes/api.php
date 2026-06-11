<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rutas se montan bajo el prefijo "/api" (ver bootstrap/app.php).
| El frontend Vue las consume desde http://crewfare.localhost/api/...
*/

// Endpoint de salud sencillo: GET /api/ping
Route::get('/ping', fn () => response()->json(['message' => 'pong']));

// Recurso de ejemplo. apiResource genera:
//   GET    /api/items        -> index
//   POST   /api/items        -> store
//   GET    /api/items/{item} -> show
//   PUT    /api/items/{item} -> update
//   DELETE /api/items/{item} -> destroy
Route::apiResource('items', ItemController::class);
