<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $users = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idRol', '=', 'roles.id')
                ->select(
                    'personas.id',
                    'personas.nombre',
                    'personas.tipo_documento',
                    'personas.num_documento',
                    'personas.direccion',
                    'personas.telefono',
                    'personas.email',
                    'users.usuario',
                    'users.password',
                    'users.email as email_recuperacion',
                    'users.condicion',
                    'users.idRol',
                    'roles.nombre as rol'
                )->orderBy('personas.nombre', 'asc')->paginate(4);
        } else {
            $users = $users = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idRol', '=', 'roles.id')
                ->select(
                    'personas.id',
                    'personas.nombre',
                    'personas.tipo_documento',
                    'personas.num_documento',
                    'personas.direccion',
                    'personas.telefono',
                    'personas.email',
                    'users.usuario',
                    'users.password',
                    'users.email as email_recuperacion',
                    'users.condicion',
                    'users.idRol',
                    'roles.nombre as rol'
                )
                ->where('personas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('personas.nombre', 'asc')->paginate(4);
        }

        return [
            'pagination' => [
                'total'         =>  $users->total(),
                'current_page'  =>  $users->currentPage(),
                'per_page'      =>  $users->perPage(),
                'last_page'     =>  $users->lastPage(),
                'from'          =>  $users->firstItem(),
                'to'            =>  $users->lastItem()
            ],  'users'    => $users
        ];
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

            $users = new User();
            $users->usuario = $request->usuario;
            $users->password = bcrypt($request->password);
            $users->email = $request->email_recuperacion;
            $users->condicion = '1';
            $users->idRol = $request->idRol;
            $users->id = $persona->id;
            $users->save();

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
            $users = User::findOrFail($request->id);
            $persona = Persona::findOrFail($users->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->update();

            $users->usuario = $request->usuario;
            $users->password = bcrypt($request->password);
            $users->email = $request->email_recuperacion;
            $users->condicion = '1';
            $users->idRol = $request->idRol;
            $users->id = $persona->id;
            $users->update();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $users = User::findOrFail($request->id);
        $users->condicion = '1';
        $users->update();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $users = User::findOrFail($request->id);
        $users->condicion = '0';
        $users->update();
    }
}
