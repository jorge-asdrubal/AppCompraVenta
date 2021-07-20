<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Persona;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
                ->select(
                    'personas.id',
                    'personas.nombre',
                    'personas.tipo_documento',
                    'personas.num_documento',
                    'personas.direccion',
                    'personas.telefono',
                    'personas.email',
                    'proveedores.contacto',
                    'proveedores.telefono_contacto'
                )->orderBy('personas.nombre', 'asc')->paginate(4);
        } else {
            $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
                ->select(
                    'personas.id',
                    'personas.nombre',
                    'personas.tipo_documento',
                    'personas.num_documento',
                    'personas.direccion',
                    'personas.telefono',
                    'personas.email',
                    'proveedores.contacto',
                    'proveedores.telefono_contacto'
                )->where('personas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('personas.nombre', 'asc')->paginate(4);
        }

        return [
            'pagination' => [
                'total'         =>  $proveedores->total(),
                'current_page'  =>  $proveedores->currentPage(),
                'per_page'      =>  $proveedores->perPage(),
                'last_page'     =>  $proveedores->lastPage(),
                'from'          =>  $proveedores->firstItem(),
                'to'            =>  $proveedores->lastItem()
            ],  'proveedores'    => $proveedores
        ];
    }

    public function selectProveedor(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $filtro = $request->filtro;
        $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->where('personas.nombre', 'like', '%' . $filtro . '%')
            ->select('personas.id', 'personas.nombre', 'personas.num_documento')
            ->orderBy('personas.nombre', 'asc')->get();
        return ['proveedores' => $proveedores];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try {
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save();

            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;
            $proveedor->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try {
            DB::beginTransaction();
            $proveedor = Proveedor::findOrFail($request->id);
            $persona = Persona::findOrFail($proveedor->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->update();

            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->update();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
