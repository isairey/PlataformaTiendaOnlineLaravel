<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//ruta de uso 

use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ControlInventario;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\GastosController;
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
    return redirect()->route('login');
});

Auth::routes();

Route::get('/register', [RegistrarController::class, 'regis'])->name('Conregister');
Route::post('/registrar', [RegistrarController::class, 'store'])->name('registrarL');


//opcion perfil
Route::get('/perfil', [App\Http\Controllers\HomeController::class, 'index'])->name('HOME');

//Sacra sctok numeor de scto para el nav 
Route::get('/Mostrarsctok', [ControlInventario::class, 'Mostrarsctok']);

///// rutas de perfil
Route::get('/perfil', [PerfilController::class, 'index']);
Route::get('/Rol', [PerfilController::class, 'Rol']);
Route::get('/Nav', [PerfilController::class, 'Nav']);
Route::get('/getMonthlyData', [PerfilController::class, 'getMonthlyData']);
Route::get('/SacarMejor', [PerfilController::class, 'SacarMejor']);
Route::get('/SacarPeor', [PerfilController::class, 'SacarPeor']);
Route::get('/MejoresClientes', [PerfilController::class, 'MejoresClientes']);



/// Rutas de Factura 
Route::get('/Factura', [FacturaController::class, 'FacturaIndex']);
Route::get('/BusquedaF', [FacturaController::class, 'BusquedaCodigo']);
Route::post('/GuardasClientes', [FacturaController::class, 'GuardasClientes']);
Route::get('/ListraUsuarios', [FacturaController::class, 'ListraUsuarios']);
Route::get('/RalizarFacturaBusqueda', [FacturaController::class, 'RalizarFacturaBusqueda']);
Route::post('/Factura', [FacturaController::class, 'Factura']);
Route::get('/SacarOrdenServicio', [FacturaController::class, 'SacarOrdenServicio']);
Route::get('/GuardarProductos', [FacturaController::class, 'GuardarProductos']);
Route::get('/CategoriasSupermercado', [FacturaController::class, 'CategoriasSupermercado']);

///Rutas de Control de invnetario 

Route::get('/ControlInventario', [ControlInventario::class, 'ControlInventarioIndex']); 
Route::get('/SacarInformacion', [ControlInventario::class, 'SacarInformacion']);
Route::delete('/EliminarFactura', [ControlInventario::class, 'EliminarFactura']);
Route::get('/SacarInformacioC', [ControlInventario::class, 'SacarInformacioC']); 
Route::get('/DevolucionProductos', [ControlInventario::class, 'DevolucionProductos']); 
Route::get('/DevolerInventario', [ControlInventario::class, 'DevolerInventario']); 

///Rutaas de control de inventario sctock
Route::get('/stock', [ControlInventario::class, 'stock']); 
Route::get( '/SacarDatosSemiAgotados', [ControlInventario::class, 'SacarDatosSemiAgotados']); 
Route::get( '/SacarDatosAgotados', [ControlInventario::class, 'SacarDatosAgotados']); 

//// Rutas de control de cantidad 
Route::get('/ControlCantidad', [ControlInventario::class, 'ControlCantidad']); 
Route::get('/SacarInformacioControlC', [ControlInventario::class, 'SacarInformacioControlC']);
Route::get('/SacarCategoria', [ControlInventario::class, 'SacarCategoria']);
Route::get('/ElementosDeCategoria', [ControlInventario::class, 'ElementosDeCategoria']);


// Rutas de ganacias
Route::get('/ganacias', [ControlInventario::class, 'ganancias']); 
Route::get('/SacarGanancias', [ControlInventario::class, 'SacarGanancias']); 
Route::get('/Roles', [ControlInventario::class, 'Roles']); 

//// inventario de los productos 
Route::get('/Inventario', [InventarioController::class, 'Inventario'])->name('Inventario'); 
Route::get('/listarinv', [InventarioController::class, 'listarinv'])->name('listarinv');
Route::get('/obtenerCategorias', [InventarioController::class, 'obtenerCategorias'])->name('obtenerCategorias');
Route::get('/referencias', [InventarioController::class, 'referencias'])->name('referencias');
Route::get('/actualizar/{id_productos}', [InventarioController::class, 'actualizar'])->name('actualizar');
Route::get('/producto/{id_productos}', [InventarioController::class, 'obtenerProducto']);
Route::get('/obtenerproductoreferencia', [InventarioController::class, 'obtenerproductoreferencia']);
Route::get('/cantidadProducto', [InventarioController::class, 'cantidadProducto']);

Route::get('/CategoriaRegister', [InventarioController::class, 'CategoriaRegister']);


Route::get('/obtenerproductoreferencia', [InventarioController::class, 'obtenerProductoPorReferencia']);
Route::get('/cantidadProducto', [InventarioController::class, 'cantidadProducto']);

Route::post('/Registrar', [InventarioController::class, 'Registrarproducto'])->name('Registrar');
Route::post('/exel', [InventarioController::class, 'exel'])->name('exel');
Route::post('/agregarproductos', [InventarioController::class, 'agregarProductos']);
Route::put('/Actualizar/{id_productos}', [InventarioController::class, 'actualizarProducto']);
Route::delete('/Eliminar/{id_productos}', [InventarioController::class, 'Eliminar']);

/// rutas de registart
Route::get('/RegistrarProductos', [InventarioController::class, 'registrar'])->name('registrar');

/// rura de agragra oroducto al almancwe
Route::get('/Agregar', [InventarioController::class, 'Agregar'])->name('Agregar'); 
Route::post('/actualizar-talla/{codigo}', [InventarioController::class, 'actualizarTalla']);


/// rutas de fucniones

Route::get('/Ventas', [InventarioController::class, 'Ventas']);
Route::get('/SacraVentas', [InventarioController::class, 'SacraVentas']);


/////clientes
Route::get('/clientes', [ClientesController::class, 'clientes'])->name('clientes');
Route::get('/datosClientesAbono', [ClientesController::class, 'datosClientesAbono']);
Route::get('/Abono', [ClientesController::class, 'Abono']);
Route::get('/listarCredito', [ClientesController::class, 'listarCredito']);
Route::get('/AbonoTotal', [ClientesController::class, 'AbonoTotal']);
Route::get('/listar', [ClientesController::class, 'listar'])->name('listar');
Route::put('/actualizarCliente/{id_cliente}', [ClientesController::class, 'actualizarCliente']);
Route::delete('/eliminarCliente/{id_cliente}', [ClientesController::class, 'eliminarCliente']);
Route::get('/buscarcliente', [ClientesController::class, 'buscarCliente']);
Route::get('/ListarPD', [ClientesController::class, 'listarproductosD']);



Route::get('/buscar-tallas', [InventarioController::class, 'buscarTallas']);
Route::get('/buscar-tallas', [InventarioController::class, 'buscarTallas']);

Route::get('/gananciaTraer', [InventarioController::class, 'gananciaTraer']);
//// rutas de modulo de movieminetos de los clientes !!! 
Route::get('/Movimientos', [MovimientosController::class, 'Index']);
Route::get('/SacarMovimientos', [MovimientosController::class, 'SacarMovimientos']);

///Gastos

Route::get('/Gastos', [GastosController::class, 'Gastos']);
Route::get('/gastosTraer', [GastosController::class, 'gastosTraer']); 
Route::post('/gastosAgregar', [GastosController::class, 'gastosAgregar']); 
Route::delete('/gastoEliminar/{id_gasto}', [GastosController::class, 'eliminarGasto']);


// gancinacia s
Route::get('/gananciasTraer', [ControlInventario::class, 'gananciaTraer']); 