<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') $categorias = Categoria::orderBy('id', 'desc')->paginate(4);
        else $categorias = Categoria::where($criterio, 'like', '%' . $buscar . '%')->orderBy('id', 'desc')->paginate(4);

        return [
            'pagination' => [
                'total'         =>  $categorias->total(),
                'current_page'  =>  $categorias->currentPage(),
                'per_page'      =>  $categorias->perPage(),
                'last_page'     =>  $categorias->lastPage(),
                'from'          =>  $categorias->firstItem(),
                'to'            =>  $categorias->lastItem()
            ],  'categorias'    => $categorias
        ];
    }

    public function listarPDF()
    {
        $categorias = Categoria::all('nombre', 'descripcion', 'condicion');

        $cont = Categoria::count();

        $pdf = \PDF::loadView('pdf.categoriaspdf', ['categorias' => $categorias, 'cont' => $cont]);

        return $pdf->download('categorias.pdf');
    }

    public function selectCategoria(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categorias = Categoria::where('condicion', '=', '1')
            ->select('id', 'nombre')->orderBy('nombre', 'asc')->get();
        return ['categorias' => $categorias];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save();
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Categoria::findOrFail($request->id);
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->update();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = '1';
        $categoria->update();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = '0';
        $categoria->update();
    }
}
