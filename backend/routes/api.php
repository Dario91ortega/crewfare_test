<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\TestController;
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

// Proxy a APIs públicas (evita CORS): GET /api/proxy?url=https://...
Route::get('/proxy', ProxyController::class);

// Endpoint de prueba genérico: GET /api/test
Route::get('/test', [TestController::class, 'index']);

// View html
Route::get('/example', [TestController::class, 'exampleHtml']);


// Recurso de ejemplo. apiResource genera:
//   GET    /api/items        -> index
//   POST   /api/items        -> store
//   GET    /api/items/{item} -> show
//   PUT    /api/items/{item} -> update
//   DELETE /api/items/{item} -> destroy
Route::apiResource('items', ItemController::class);
