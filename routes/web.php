<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\TiposInsumosController;
use App\Http\Controllers\Admin\InsumoController;
use App\Http\Controllers\Admin\ClienteController;
use App\Models\Cliente;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login'); 
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

/*Route::middleware(['auth'])->group(function () {
    Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class);
});*/

Auth::routes();

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('proveedores', App\Http\Controllers\Admin\ProveedorController::class);
    Route::resource('tipo-insumos', App\Http\Controllers\Admin\TiposInsumosController::class)
        ->except(['destroy']);
    Route::resource('insumos', App\Http\Controllers\Admin\InsumoController::class)
        ->except(['destroy']);
    Route::resource('productos', App\Http\Controllers\Admin\ProductoController::class)
        ->parameters(['productos' => 'producto']);
    Route::get('productos/{id}/insumos', [App\Http\Controllers\Admin\ProductoController::class, 'verInsumos'])->name('productos.insumos');
    Route::resource('clientes', App\Http\Controllers\Admin\ClienteController::class);
    Route::resource('almacenes', App\Http\Controllers\Admin\AlmacenController::class)
    ->parameters(['almacenes' => 'almacen']);
    
    // COtizaciones
    Route::resource('cotizaciones', App\Http\Controllers\Admin\CotizacionController::class)
    ->parameters(['cotizaciones' => 'cotizacion']);


    Route::get('/clientes/{cliente}/cotizacion-simulada', function (Cliente $cliente) {
        return view('admin.clientes.simulada', ['cliente' => $cliente]);
    })->name('cotizacion.simulada');
    
});

