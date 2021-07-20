<?php

namespace App\Http\Controllers;

use App\Models\DetalleIngreso;
use App\Models\Ingreso;
use App\Models\User;
use App\Notifications\NotifyAdmin;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select(
                    'ingresos.id',
                    'ingresos.tipo_comprobante',
                    'ingresos.serie_comprobante',
                    'ingresos.num_comprobante',
                    'ingresos.fecha_hora',
                    'ingresos.impuesto',
                    'ingresos.total',
                    'ingresos.estado',
                    'personas.nombre',
                    'users.usuario',
                )->orderBy('ingresos.id', 'desc')->paginate(4);
        } else {
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select(
                    'ingresos.id',
                    'ingresos.tipo_comprobante',
                    'ingresos.serie_comprobante',
                    'ingresos.num_comprobante',
                    'ingresos.fecha_hora',
                    'ingresos.impuesto',
                    'ingresos.total',
                    'ingresos.estado',
                    'personas.nombre',
                    'users.usuario',
                )
                ->where('ingresos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('ingresos.id', 'desc')->paginate(4);
        }

        return [
            'pagination' => [
                'total'         =>  $ingresos->total(),
                'current_page'  =>  $ingresos->currentPage(),
                'per_page'      =>  $ingresos->perPage(),
                'last_page'     =>  $ingresos->lastPage(),
                'from'          =>  $ingresos->firstItem(),
                'to'            =>  $ingresos->lastItem()
            ],  'ingresos'    => $ingresos
        ];
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request->id;
        $ingreso = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
            ->join('users', 'ingresos.idusuario', '=', 'users.id')
            ->select(
                'ingresos.id',
                'ingresos.tipo_comprobante',
                'ingresos.serie_comprobante',
                'ingresos.num_comprobante',
                'ingresos.fecha_hora',
                'ingresos.impuesto',
                'ingresos.total',
                'ingresos.estado',
                'personas.nombre',
                'users.usuario',
            )
            ->where('ingresos.id', '=', $id)->take(1)->get();

        return ['ingreso'    => $ingreso];
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request->id;
        $detalles = DetalleIngreso::join('articulos', 'detalle_ingresos.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ingresos.cantidad',
                'detalle_ingresos.precio',
                'articulos.nombre as articulo',
            )
            ->where('detalle_ingresos.idingreso', '=', $id)
            ->orderBy('articulos.nombre', 'asc')->get();

        return ['detalles'    => $detalles];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try {
            DB::beginTransaction();

            $fecha_hora = Carbon::now('America/Bogota');

            $ingreso = new Ingreso();
            $ingreso->idproveedor = $request->idproveedor;
            $ingreso->idusuario = \Auth::user()->id;
            $ingreso->tipo_comprobante = $request->tipo_comprobante;
            $ingreso->serie_comprobante = $request->serie_comprobante;
            $ingreso->num_comprobante = $request->num_comprobante;
            $ingreso->fecha_hora = $fecha_hora->toDateString();
            $ingreso->impuesto = $request->impuesto;
            $ingreso->total = $request->total;
            $ingreso->estado = 'Registrado';
            $ingreso->save();

            $detalles = $request->data; //Array con todos los detalles del ingreso
            foreach ($detalles as $det) {
                $detalle = new DetalleIngreso();
                $detalle->idingreso = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->save();
            }

            $fechaActual = date('Y-m-d');
            $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
            $numIngresos = DB::table('ingresos')->whereDate('created_at', $fechaActual)->count();

            $arregloDatos = [
                'ventas' => [
                    'numero' => $numVentas,
                    'msj' => 'Ventas'
                ],
                'ingresos' => [
                    'numero' => $numIngresos,
                    'msj' => 'Ingresos'
                ]
            ];

            $allUsers = User::all();

            foreach ($allUsers as $notificar) {
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /* public function update(Request $request)
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
    } */ //Hacer el update por mi cuenta

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $ingresos = Ingreso::findOrFail($request->id);
        $ingresos->estado = 'Anulado';
        $ingresos->update();
    }
}
