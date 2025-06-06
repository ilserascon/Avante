<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\TiposInsumosController;
use App\Http\Controllers\Admin\InsumoController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\AlmacenController;
use App\Http\Controllers\Admin\CotizacionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login'); 
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

// Rutas SOLO para Administrador
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/habilitar', [App\Http\Controllers\Admin\UserController::class, 'habilitar'])->name('users.habilitar');
    Route::resource('tipo-insumos', TiposInsumosController::class)->except(['destroy']);
    Route::resource('insumos', InsumoController::class)->except(['destroy']);
    Route::get('insumos/campos-dinamicos', [InsumoController::class, 'camposDinamicosPorTipo'])->name('insumos.campos-dinamicos');
    Route::resource('productos', ProductoController::class)->parameters(['productos' => 'producto']);
    Route::get('productos/{id}/insumos', [ProductoController::class, 'verInsumos'])->name('productos.insumos');
    Route::resource('clientes', ClienteController::class);
    Route::resource('cotizaciones', App\Http\Controllers\Admin\CotizacionController::class)
        ->parameters(['cotizaciones' => 'cotizacion']);
    Route::post('cotizaciones/{cotizacion}/cambiar-estatus', [CotizacionController::class, 'cambiarEstatus'])->name('cotizaciones.cambiar-estatus');
    Route::get('cotizaciones/{cotizacion}/pdf', [CotizacionController::class, 'generarPdf'])->name('cotizaciones.pdf');
    Route::get('cotizaciones/{cotizacion}/pdfdecorador', [CotizacionController::class, 'pdfDecorador'])->name('cotizaciones.pdf-decorador');


});

// Rutas para Administrador y Almacén
Route::middleware(['auth', 'role:Administrador,Almacén,Almacen'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('proveedores', ProveedorController::class);
    Route::post('proveedores/{proveedor}/habilitar', [App\Http\Controllers\Admin\ProveedorController::class, 'habilitar'])->name('proveedores.habilitar');
    Route::resource('almacenes', AlmacenController::class)->parameters(['almacenes' => 'almacen']);
    Route::get('/almacenes/{id}/existencia', [AlmacenController::class, 'showExistencia'])->name('almacenes.existencia');
    Route::resource('entradas', App\Http\Controllers\Admin\EntradaController::class);
});

// Rutas para Administrador y Cotizador
Route::middleware(['auth', 'role:Administrador,Cotizador'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('insumos', App\Http\Controllers\Admin\InsumoController::class)->except(['destroy']);
    Route::resource('productos', App\Http\Controllers\Admin\ProductoController::class)->parameters(['productos' => 'producto']);
    Route::get('productos/{id}/insumos', [ProductoController::class, 'verInsumos'])->name('productos.insumos');
    Route::resource('clientes', App\Http\Controllers\Admin\ClienteController::class);
    Route::post('clientes/{cliente}/habilitar', [App\Http\Controllers\Admin\ClienteController::class, 'habilitar'])->name('clientes.habilitar');
    Route::resource('cotizaciones', App\Http\Controllers\Admin\CotizacionController::class)
        ->parameters(['cotizaciones' => 'cotizacion']);
    Route::post('cotizaciones/{cotizacion}/cambiar-estatus', [CotizacionController::class, 'cambiarEstatus'])->name('cotizaciones.cambiar-estatus');
    Route::get('cotizaciones/{cotizacion}/pdf', [CotizacionController::class, 'generarPdf'])->name('cotizaciones.pdf');
    Route::get('cotizaciones/{cotizacion}/pdfdecorador', [CotizacionController::class, 'pdfDecorador'])->name('cotizaciones.pdf-decorador');
});