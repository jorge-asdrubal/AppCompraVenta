<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/main', function () {
        return view('contenido');
    })->name('main');

    Route::get('/dashboard', [DashboardController::class, '__invoke']);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('notification/get', [NotificationController::class, 'get']);

    Route::middleware(['almacenero'])->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index']);
        Route::get('/categorias/listarPDF', [CategoriaController::class, 'listarPDF']);
        Route::post('/categorias/registrar', [CategoriaController::class, 'store']);
        Route::put('/categorias/actualizar', [CategoriaController::class, 'update']);
        Route::put('/categorias/activar', [CategoriaController::class, 'activar']);
        Route::put('/categorias/desactivar', [CategoriaController::class, 'desactivar']);
        Route::get('/categorias/selectCategoria', [CategoriaController::class, 'selectCategoria']);

        Route::get('/articulos', [ArticuloController::class, 'index']);
        Route::get('/articulos/buscarArticulos', [ArticuloController::class, 'buscarArticulos']);
        Route::get('/articulos/listarPDF', [ArticuloController::class, 'listarPDF']);
        Route::get('/articulos/listarArticulos', [ArticuloController::class, 'listarArticulos']);
        Route::post('/articulos/registrar', [ArticuloController::class, 'store']);
        Route::put('/articulos/actualizar', [ArticuloController::class, 'update']);
        Route::put('/articulos/activar', [ArticuloController::class, 'activar']);
        Route::put('/articulos/desactivar', [ArticuloController::class, 'desactivar']);

        Route::get('/proveedores', [ProveedorController::class, 'index']);
        Route::post('/proveedores/registrar', [ProveedorController::class, 'store']);
        Route::put('/proveedores/actualizar', [ProveedorController::class, 'update']);
        Route::get('/proveedores/selectProveedor', [ProveedorController::class, 'selectProveedor']);

        Route::get('/ingresos', [IngresoController::class, 'index']);
        Route::get('/ingresos/obtenerCabecera', [IngresoController::class, 'obtenerCabecera']);
        Route::get('/ingresos/obtenerDetalles', [IngresoController::class, 'obtenerDetalles']);
        Route::post('/ingresos/registrar', [IngresoController::class, 'store']);
        Route::put('/ingresos/desactivar', [IngresoController::class, 'desactivar']);
    });

    Route::middleware(['administrador'])->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index']);
        Route::get('/categorias/listarPDF', [CategoriaController::class, 'listarPDF']);
        Route::post('/categorias/registrar', [CategoriaController::class, 'store']);
        Route::put('/categorias/actualizar', [CategoriaController::class, 'update']);
        Route::put('/categorias/activar', [CategoriaController::class, 'activar']);
        Route::put('/categorias/desactivar', [CategoriaController::class, 'desactivar']);
        Route::get('/categorias/selectCategoria', [CategoriaController::class, 'selectCategoria']);

        Route::get('/articulos', [ArticuloController::class, 'index']);
        Route::get('/articulos/buscarArticulos', [ArticuloController::class, 'buscarArticulos']);
        Route::get('/articulos/listarPDF', [ArticuloController::class, 'listarPDF']);
        Route::get('/articulos/buscarArticulosVenta', [ArticuloController::class, 'buscarArticulosVenta']);
        Route::get('/articulos/listarArticulos', [ArticuloController::class, 'listarArticulos']);
        Route::get('/articulos/listarArticulosVenta', [ArticuloController::class, 'listarArticulosVenta']);
        Route::post('/articulos/registrar', [ArticuloController::class, 'store']);
        Route::put('/articulos/actualizar', [ArticuloController::class, 'update']);
        Route::put('/articulos/activar', [ArticuloController::class, 'activar']);
        Route::put('/articulos/desactivar', [ArticuloController::class, 'desactivar']);

        Route::get('/proveedores', [ProveedorController::class, 'index']);
        Route::post('/proveedores/registrar', [ProveedorController::class, 'store']);
        Route::put('/proveedores/actualizar', [ProveedorController::class, 'update']);
        Route::get('/proveedores/selectProveedor', [ProveedorController::class, 'selectProveedor']);

        Route::get('/ingresos', [IngresoController::class, 'index']);
        Route::get('/ingresos/obtenerCabecera', [IngresoController::class, 'obtenerCabecera']);
        Route::get('/ingresos/obtenerDetalles', [IngresoController::class, 'obtenerDetalles']);
        Route::post('/ingresos/registrar', [IngresoController::class, 'store']);
        Route::put('/ingresos/desactivar', [IngresoController::class, 'desactivar']);

        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::get('/clientes/selectCliente', [ClienteController::class, 'selectCliente']);
        Route::post('/clientes/registrar', [ClienteController::class, 'store']);
        Route::put('/clientes/actualizar', [ClienteController::class, 'update']);

        Route::get('/ventas', [VentaController::class, 'index']);
        Route::get('/ventas/pdf/{id}', [VentaController::class, 'pdf'])->name('venta_pdf');
        Route::get('/ventas/obtenerCabecera', [VentaController::class, 'obtenerCabecera']);
        Route::get('/ventas/obtenerDetalles', [VentaController::class, 'obtenerDetalles']);
        Route::post('/ventas/registrar', [VentaController::class, 'store']);
        Route::put('/ventas/desactivar', [VentaController::class, 'desactivar']);

        Route::get('/roles', [RolController::class, 'index']);
        Route::get('/roles/selectRol', [RolController::class, 'selectRol']);

        Route::get('/usuarios', [UserController::class, 'index']);
        Route::post('/usuarios/registrar', [UserController::class, 'store']);
        Route::put('/usuarios/actualizar', [UserController::class, 'update']);
        Route::put('/usuarios/activar', [UserController::class, 'activar']);
        Route::put('/usuarios/desactivar', [UserController::class, 'desactivar']);
    });

    Route::middleware(['vendedor'])->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::get('/clientes/selectCliente', [ClienteController::class, 'selectCliente']);
        Route::post('/clientes/registrar', [ClienteController::class, 'store']);
        Route::put('/clientes/actualizar', [ClienteController::class, 'update']);

        Route::get('/articulos/buscarArticulosVenta', [ArticuloController::class, 'buscarArticulosVenta']);
        Route::get('/articulos/listarArticulosVenta', [ArticuloController::class, 'listarArticulosVenta']);

        Route::get('/ventas', [VentaController::class, 'index']);
        Route::get('/ventas/pdf/{id}', [VentaController::class, 'pdf'])->name('venta_pdf');
        Route::get('/ventas/obtenerCabecera', [VentaController::class, 'obtenerCabecera']);
        Route::get('/ventas/obtenerDetalles', [VentaController::class, 'obtenerDetalles']);
        Route::post('/ventas/registrar', [VentaController::class, 'store']);
        Route::put('/ventas/desactivar', [VentaController::class, 'desactivar']);
    });
});
