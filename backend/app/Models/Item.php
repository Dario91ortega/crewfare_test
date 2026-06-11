<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * Atributos asignables en masa (create/update con un array).
     */
    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    /**
     * Casts: Laravel convierte estos campos al tipo indicado
     * al leerlos/escribirlos (price llega como float en el JSON).
     */
    protected $casts = [
        'price' => 'float',
    ];
}
