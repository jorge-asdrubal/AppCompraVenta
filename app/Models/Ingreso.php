<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $filiable = [
        'idproveedor',
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Models\Proveedor');
    }
}
