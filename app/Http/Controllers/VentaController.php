<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\DetalleVenta;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario',
                )->orderBy('ventas.id', 'desc')->paginate(4);
        } else {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario',
                )
                ->where('ventas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('ventas.id', 'desc')->paginate(4);
        }

        return [
            'pagination' => [
                'total'         =>  $ventas->total(),
                'current_page'  =>  $ventas->currentPage(),
                'per_page'      =>  $ventas->perPage(),
                'last_page'     =>  $ventas->lastPage(),
                'from'          =>  $ventas->firstItem(),
                'to'            =>  $ventas->lastItem()
            ],  'ventas'    => $ventas
        ];
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request->id;
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'personas.nombre',
                'users.usuario',
            )
            ->where('ventas.id', '=', $id)->take(1)->get();

        return ['venta'    => $venta];
    }

    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id = $request->id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo',
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('articulos.nombre', 'asc')->get();

        return ['detalles'    => $detalles];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try {
            DB::beginTransaction();

            $fecha_hora = Carbon::now('America/Bogota');

            $venta = new Venta();
            $venta->idcliente = $request->idcliente;
            $venta->idusuario = \Auth::user()->id;
            $venta->tipo_comprobante = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante = $request->num_comprobante;
            $venta->fecha_hora = $fecha_hora->toDateString();
            $venta->impuesto = $request->impuesto;
            $venta->total = $request->total;
            $venta->estado = 'Registrado';
            $venta->save();

            $detalles = $request->data; //Array con todos los detalles del venta
            foreach ($detalles as $det) {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];
                $detalle->save();
            }

            DB::commit();
            return ['id' => $venta->id];
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function pdf(Request $request, $id)
    {
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'ventas.created_at',
                'personas.nombre',
                'personas.tipo_documento',
                'personas.num_documento',
                'personas.direccion',
                'personas.email',
                'personas.telefono',
                'users.usuario',
            )->where('ventas.id', '=', $id)->take(1)->get();

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo',
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        $numventa = Venta::select('num_comprobante')->where('id', $id)->get();

        $pdf = \PDF::loadView('pdf.ventaspdf', ['venta' => $venta, 'detalles' => $detalles]);
        return $pdf->download('venta-' . $numventa[0]->num_comprobante . '.pdf');
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
        $ventas = Venta::findOrFail($request->id);
        $ventas->estado = 'Anulado';
        $ventas->update();
    }
}
