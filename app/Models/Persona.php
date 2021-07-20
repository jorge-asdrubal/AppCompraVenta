<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $filiable = ['nombre', 'tipo_documento', 'num_documento', 'direccion', 'telefono', 'email'];

    public function proveedor()
    {
        return $this->hasOne('App\Models\Proveedor');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }
}
