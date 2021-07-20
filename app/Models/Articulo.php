<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Articulo extends Model
{
    use HasFactory;
    protected $filiable = [
        'idcategoria',
        'codigo',
        'nombre',
        'precio_venta',
        'stock',
        'descripcion',
        'condicion'
    ];

    public function categoria()
    {
        return $this->BelongsTo('App\Models\Categoria');
    }
}
