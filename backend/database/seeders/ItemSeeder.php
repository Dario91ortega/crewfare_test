<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Inserta datos de ejemplo en la tabla items.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Camiseta Crewfare', 'description' => 'Camiseta de algodón 100%', 'price' => 19.99],
            ['name' => 'Taza de café',      'description' => 'Taza cerámica 350ml',      'price' => 9.50],
            ['name' => 'Sticker pack',      'description' => 'Pack de 10 stickers',      'price' => 4.00],
            ['name' => 'Gorra',             'description' => 'Gorra ajustable',          'price' => 14.25],
            ['name' => 'Botella térmica',   'description' => 'Acero inoxidable 500ml',   'price' => 24.90],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
