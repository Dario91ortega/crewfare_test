<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    /**
     * Proxy para consumir APIs públicas sin sufrir CORS.
     *
     * El frontend llama a GET /api/proxy?url=<url-externa> (mismo origen, sin
     * CORS) y Laravel hace la petición a esa URL desde el servidor (donde CORS
     * no aplica) y devuelve la respuesta tal cual.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $url = $request->query('url');

        // Validamos que venga una URL http(s) válida.
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Parámetro "url" inválido'], 422);
        }

        // Lista blanca de hosts permitidos. SIN esto tendrías un "open proxy":
        // cualquiera podría usar tu backend para pegarle a servicios internos
        // (vulnerabilidad SSRF). En producción esto es OBLIGATORIO.
        $allowed = [
            'rickandmortyapi.com',
            'pokeapi.co',
            'restcountries.com',
            'jsonplaceholder.typicode.com',
            'api.coingecko.com',
        ];

        $host = parse_url($url, PHP_URL_HOST);
        if (! in_array($host, $allowed, true)) {
            return response()->json(['error' => "Host no permitido: {$host}"], 403);
        }

        // Petición server-side. timeout evita que una API lenta cuelgue tu API.
        $response = Http::timeout(10)
            ->acceptJson()
            ->get($url);

        // Reenviamos el cuerpo y el código de estado de la API externa.
        return response()->json(
            $response->json(),
            $response->status()
        );
    }
}
