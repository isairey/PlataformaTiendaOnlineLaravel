<?php

namespace App\Http\Controllers;

use App\Models\GananciasDiarias;
use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\FacturasEliminadas;
use App\Models\Clientes;
use App\Models\ProductosModel;
use App\Models\Producto;
use Carbon\Carbon;
use App\Models\vendidos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ControlInventario extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function ControlInventarioIndex(Request $request){

        return view ('Administrador/Controlinventario');
    }
    public function sacarInformacion(Request $request)
    {
        $orden_servicio = $request->input('orden_servicio');
        $query = Factura::query();
        if (!empty($orden_servicio)) {
            $query->where('orden_servicio', 'like', "%{$orden_servicio}%");
        }
        $resultados = $query->orderBy('orden_servicio', 'desc')->paginate(15);
        // Retorna los resultados con paginación
   
     
        return response()->json([
            'pagination' => [
                'total' => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page' => $resultados->perPage(),
                'last_page' => $resultados->lastPage(),
                'from' => $resultados->firstItem(),
                'to' => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items(),
        ]);
    }
    public function EliminarFactura(Request $request){ //// Eliminar Factura y guarda en facturas eliminadas !!!
        $orden_servicio = $request->orden_servicio; 
        $Problema = $request->Problema;

        $GuardarDatos = Factura::where('orden_servicio', $orden_servicio)->first();
        $GuardarDatosBorrados = new FacturasEliminadas();
        $GuardarDatosBorrados->orden_servicio = $GuardarDatos->orden_servicio;
        $GuardarDatosBorrados->nombre = $GuardarDatos->nombre;
        $GuardarDatosBorrados->documento = $GuardarDatos->documento;
        $GuardarDatosBorrados->descripcion = $GuardarDatos->descripcion; 
        $GuardarDatosBorrados->total = $GuardarDatos->totalF;
        $GuardarDatosBorrados->fecha_de_creacion = $GuardarDatos->fecha_de_creacion;
        $GuardarDatosBorrados->Problema =  $Problema;

        $GuardarDatosBorrados->save();
        $Eliminar = Factura::where('orden_servicio', $orden_servicio)->delete();

        GananciasDiarias::where('fecha_de_creacion', $GuardarDatos->fecha_de_creacion)->delete();

    }
    public function SacarInformacioC(Request $request)
    {
    $documento = $request->documento;
    $orden_servicio = $request->orden_servicio;

    $cliente = Clientes::where('documento', $documento)->get();
    $totalF = 0;
    $descripcionArray = [];

    $factura = Factura::where('orden_servicio', $orden_servicio)->first();

    if ($factura) {
        $descripcionJson = $factura->descripcion;
        $descripcionArray = json_decode($descripcionJson, true);   

        foreach ($descripcionArray as $item) {
            if (isset($item['total'])) {
                $totalF += $item['total'];
            }
        }
    }

    return response()->json([
        'cliente' => $cliente,
        'descripcion' => $descripcionArray,
        'TotalF' => $totalF,
        'orden' => $orden_servicio,
    ]);
    }
    public function DevolucionProductos(Request $request)// traer los datos e la factura para realizar la devolucion
    {
        $orden_servicio = $request->orden_servicio;
        $datos = Factura::where('orden_servicio', $orden_servicio)->first();
    
        if ($datos) {
            $descripcionArray = json_decode($datos->descripcion, true);
    
            return response()->json([
                'orden_servicio' => $orden_servicio,
                'descripcion' => $descripcionArray
            ]);
        } else {
            return response()->json([
                'error' => 'No data found for the given orden_servicio'
            ], 404);
        }
    }
    
    public function DevolerInventario(Request $request) { /// eliminar el producto y volver a poner en el invneatrio
       
        $orden_servicio = $request->orden_servicio;

        $todo = $request->todo; // Array de productos recibido
        $resultado = []; // Para almacenar el resultado

        $totalKilos = 0; // Para la suma total de kilos

        $factura = Factura::where('orden_servicio', $orden_servicio)->first();
        $descripcion = json_decode($factura->descripcion, true);
        $productos = [];

        /// sacar el precio del prdocto que es invidivios ejemplo arroz.. 
        foreach ($descripcion as $producto) {
            $id_producto = $producto['id_productos'];
            $precio = $producto['precio'];
            $productos[] = [
                'id_producto' => $id_producto, 
                'precio' => $precio,
            ];
        }



        $total = 0;

        foreach ($descripcion as $producto) {
            $id_producto = $producto['id_productos'];
            $precio = $producto['precio'];
            
            // Filtrar `$todo` para encontrar los elementos con el mismo id_producto y estado = 0
            foreach ($todo as $item) {
                if ($item['id_productos'] == $id_producto && $item['estado'] === "0") {
                    $devolucion = $item['devolucion'];
                    $total += $precio * $devolucion; // Multiplica precio por devolución y acumula el total
                }
            }
        }




        //// todo bien hasta aaqui...

        $Guardar = new GananciasDiarias();
        $Guardar->documento =  $factura->documento;
        $Guardar->Total_G = $total * -1;
        $Guardar->mp = 10;
        $Guardar->save();
        

        /// se resta el total con el total completo ysi esa resta me da da 0 o numeor < a 0 queda en 0 
        $factura = Factura::where('orden_servicio', $orden_servicio)->first();
        if ($factura) {
            $factura->totalF -= $total;
            if ($factura->totalF < 0) {
                $factura->totalF = 0;
            }
        }
            $factura->save();

        //// funciona miento para que me sume los kilos que voy a devolver al inventario !!! 
        foreach ($todo as $producto) {
            $productoDb = DB::table('productos')->where('id_productos', $producto['id_productos'])->first();
            if ($productoDb) {
                $cantidad = $producto['devolucion']; // Cantidad inicial
                $unidadPeso = $producto['unidadDePeso'] ?? null; // Unidad de peso del array recibido
    
                if (!empty($unidadPeso)) {
                    switch ($unidadPeso) {
                        case 'gramos':
                            $cantidad /= 1000;
                            break;
                        case 'miligramos':
                            $cantidad /= 1000000;
                            break;
                        case 'libras':
                            $cantidad *= 0.453592;
                            break;
                        case 'onzas':
                            $cantidad *= 0.0283495;
                            break;
                        case 'toneladas':
                            $cantidad *= 1000;
                            break;
                        case 'kilogramos':

                            break;
                        default:
                            throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                    }
                }
                $resultado[] = [
                    'id_productos' => $producto['id_productos'],
                    'cantidad' => $cantidad,
                    'total' => $producto['precio'] ?? 0,
                ];
            }
        }

        $productos = $resultado;

        /// funciona miento para que me sume los kilos que voy a devolver al inventario !!! 

        foreach ($productos as $item) {
        $productoDb = DB::table('productos')->where('id_productos', $item['id_productos'])->first();
            if ($productoDb) {
                $nuevaCantidad = $productoDb->cantidad + $item['cantidad'];
                $SumaTotal = $productoDb->precio +  $item['total'];
                DB::table('productos')
                    ->where('id_productos', $item['id_productos'])
                    ->update(['cantidad' => $nuevaCantidad, 'precio' => $SumaTotal]);

            }
        }

        /// todo bien hasta aqui .....
    
        /// funcion de restra al producto en la factura !!! 
        $factura = Factura::where('orden_servicio', $orden_servicio)->first();

        if ($factura) {

          $DatosAlRestar = $resultado;

          // Decodificar el JSON de la descripción

          $descripcion = json_decode($factura->descripcion, true);
      
           foreach ($todo as $dato) {
   
            foreach ($descripcion as $key => &$producto) { // Usamos $key para identificar el índice

                if ((int)$dato['id_productos'] === (int)$producto['id_productos']) {
                  
                  if($producto['estado'] == 1){

                    $unidadPeso = $dato['unidadDePeso'];
                    $UP =  $producto['UP'];
                    if (!empty($unidadPeso)) {
                        if ($unidadPeso == 'gramos') {
                            switch ($UP) {
                                case 'kilogramos':
                                    $dato['devolucion'] /= 1000; // Convertir gramos a kilogramos
                                    break;
                                case 'miligramos':
                                    $dato['devolucion'] *= 1000; // Convertir gramos a miligramos
                                    break;
                                case 'libras':
                                    $dato['devolucion'] *= 0.00220462; // Convertir gramos a libras
                                    break;
                                case 'onzas':
                                    $dato['devolucion'] *= 0.035274; // Convertir gramos a onzas
                                    break;
                                case 'toneladas':
                                    $dato['devolucion'] /= 1000000; // Convertir gramos a toneladas
                                    break;
                                case 'gramos':
                                    // No es necesario hacer nada si ya está en gramos
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }
                        if ($unidadPeso == 'kilogramos') {
                            switch ($UP) {
                                case 'miligramos':
                                    $dato['devolucion'] *= 1000000; // Convertir kilogramos a miligramos
                                    break;
                                case 'gramos':
                                    $dato['devolucion'] *= 1000; // Convertir kilogramos a gramos
                                    break;
                                case 'libras':
                                    $dato['devolucion'] *= 2.20462; // Convertir kilogramos a libras
                                    break;
                                case 'onzas':
                                    $dato['devolucion'] *= 35.274; // Convertir kilogramos a onzas
                                    break;
                                case 'toneladas':
                                    $dato['devolucion'] /= 1000; // Convertir kilogramos a toneladas
                                    break;
                                case 'kilogramos':
                                    // No es necesario hacer nada si ya está en kilogramos
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }
                        
                        if ($unidadPeso == 'libras') {
                            switch ($UP) {
                                case 'miligramos':
                                    $dato['devolucion'] *= 453592.37; // Convertir libras a miligramos
                                    break;
                                case 'gramos':
                                    $dato['devolucion'] *= 453.59237; // Convertir libras a gramos
                                    break;
                                case 'libras':
                                    // No es necesario hacer nada si ya está en libras
                                    break;
                                case 'onzas':
                                    $dato['devolucion'] *= 16; // Convertir libras a onzas
                                    break;
                                case 'toneladas':
                                    $dato['devolucion'] /= 2204.62; // Convertir libras a toneladas
                                    break;
                                case 'kilogramos':
                                    $dato['devolucion'] /= 2.20462; // Convertir libras a kilogramos
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }
                        if ($unidadPeso == 'miligramos') {
                            switch ($UP) {
                                case 'gramos':
                                    $dato['devolucion'] /= 1000; // Convertir miligramos a gramos
                                    break;
                                case 'kilogramos':
                                    $dato['devolucion'] /= 1000000; // Convertir miligramos a kilogramos
                                    break;
                                case 'libras':
                                    $dato['devolucion'] /= 453592.37; // Convertir miligramos a libras
                                    break;
                                case 'onzas':
                                    $dato['devolucion'] /= 28349.5; // Convertir miligramos a onzas
                                    break;
                                case 'toneladas':
                                    $dato['devolucion'] /= 1e+9; // Convertir miligramos a toneladas
                                    break;
                                case 'miligramos':
                                    // No es necesario hacer nada si ya está en miligramos
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }
                        
                        if ($unidadPeso == 'onzas') {
                            switch ($UP) {
                                case 'miligramos':
                                    $dato['devolucion'] *= 28349.5; // Convertir onzas a miligramos
                                    break;
                                case 'gramos':
                                    $dato['devolucion'] *= 28.3495; // Convertir onzas a gramos
                                    break;
                                case 'libras':
                                    $dato['devolucion'] /= 16; // Convertir onzas a libras
                                    break;
                                case 'toneladas':
                                    $dato['devolucion'] /= 35274; // Convertir onzas a toneladas
                                    break;
                                case 'kilogramos':
                                    $dato['devolucion'] /= 35.274; // Convertir onzas a kilogramos
                                    break;
                                case 'onzas':
                                    // No es necesario hacer nada si ya está en onzas
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }
                        if ($unidadPeso == 'toneladas') {
                            switch ($UP) {
                                case 'miligramos':
                                    $dato['devolucion'] *= 1e+9; // Convertir toneladas a miligramos
                                    break;
                                case 'gramos':
                                    $dato['devolucion'] *= 1e+6; // Convertir toneladas a gramos
                                    break;
                                case 'libras':
                                    $dato['devolucion'] *= 2204.62; // Convertir toneladas a libras
                                    break;
                                case 'onzas':
                                    $dato['devolucion'] *= 35274; // Convertir toneladas a onzas
                                    break;
                                case 'kilogramos':
                                    $dato['devolucion'] *= 1000; // Convertir toneladas a kilogramos
                                    break;
                                case 'toneladas':
                                    // No es necesario hacer nada si ya está en toneladas
                                    break;
                                default:
                                    throw new \Exception("Unidad de peso no válida: {$unidadPeso}");
                            }
                        }     
                    }
                }
      
                    $producto['cantidad'] -= $dato['devolucion'];
                    
                    if( $producto['estado'] == 0){
         
                    $Total =  $producto['precio'] * $dato['devolucion'];
                    $producto['total'] -=$Total;

                    }else{
                    
                    $producto['total'] -= $dato['precio'];
                    }

                    if ($producto['cantidad'] <= 0) {
                        unset($descripcion[$key]);
                    }
                }
            }
        }
        
        $factura->descripcion = json_encode($descripcion);
        $factura->save();
              
            return response()->json(['message' => 'Productos eliminados correctamente.']);
        } else {
            return response()->json(['message' => 'Formato de descripción incorrecto.'], 404);
        }
    } 
     /////Rutas de stock
    public function stock(Request $request){

    return view ('Administrador/stock');
    }
    public function SacarDatosSemiAgotados() /// Contar los produtos para mira cuales etsa agotados 
        {

            $productosSemiAgotados = \DB::table('productos')
            ->get();
        
             return  $productosSemiAgotados;
        
        }

     //// funciones de control de invnetario de cantidad
     public function ControlCantidad (Request $request){

        return view ('Administrador/ControlC');
    }
    public function SacarInformacioControlC(Request $request)
    {
        $referencia = $request->Referencia;
        $perPage = 12;
    
        $query = ProductosModel::query()
        ->join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria') // Unir tablas
        ->select('productos.*', 'categorias.nombre as categoria_nombre');
    
        if ($referencia) {
            $query->where('referencia', $referencia);
        }
    
        $resultados = $query->paginate($perPage);
    

    
        return response()->json([
            'pagination' => [
                'total'        => $resultados->total(),
                'current_page' => $resultados->currentPage(),
                'per_page'     => $resultados->perPage(),
                'last_page'    => $resultados->lastPage(),
                'from'         => $resultados->firstItem(),
                'to'           => $resultados->lastItem(),
            ],
            'resultados' => $resultados->items(),
        ]);
    }
    
    
    /// funcones de ganacias 
    public function ganancias (Request $request){
        return view ('Administrador/ganancia');
    }
    public function SacarGanancias(Request $request) // Sacar todas las facturas para contar sus ganancias
{
    $fecha_inicial = $request->input('startDate');
    $fecha_final = $request->input('endDate');
    $perPage = $request->get('per_page', default: 11);

    // Construimos la consulta sin ejecutar `get()` todavía
    $query = GananciasDiarias::query()
        ->join('clientes', 'ganancias_diarias.documento', '=', 'clientes.documento')
        ->select(
            'ganancias_diarias.*',
            'clientes.nombre as nombre_cliente',
           
        );

    // Aplicamos el filtro por fechas si se proporciona
    if ($fecha_inicial && $fecha_final) {
        $fecha_inicial = Carbon::createFromFormat('Y-m-d', $fecha_inicial)->startOfDay();
        $fecha_final = Carbon::createFromFormat('Y-m-d', $fecha_final)->endOfDay();
    
        $query->whereBetween('ganancias_diarias.fecha_de_creacion', [$fecha_inicial, $fecha_final]);
    }

    // Agregamos orden y paginación
   
    $query->orderBy('ganancias_diarias.id', 'desc');
    $datosFacturas = $query->paginate($perPage);

    // Calculamos el total general y total por el rango de fechas
    $totalGeneral = GananciasDiarias::sum('Total_G'); // Total general de todas las ganancias
    $totalPrecio = $query->sum('Total_G'); // Total de las ganancias filtradas por fecha

    return response()->json([
        'pagination' => [
            'total'        => $datosFacturas->total(),
            'current_page' => $datosFacturas->currentPage(),
            'per_page'     => $datosFacturas->perPage(),
            'last_page'    => $datosFacturas->lastPage(),
            'from'         => $datosFacturas->firstItem(),
            'to'           => $datosFacturas->lastItem(),
        ],
        'totalGeneral' => $totalGeneral,
        'totalPrecio'  => $totalPrecio,
        'data' => $datosFacturas->items(),
    ]);
}

    public function Roles() {//// sacra roles para hacer validacion
       
        $user = Auth::user();
    
        return response()->json([
            'rol' => $user ? $user->rol : null, 
        ]);
    }
    
    public function Mostrarsctok(){
        
        $productosAgrupados = \DB::table('productos')
        ->having('cantidad', '<=', 5)
        ->get();

      
        $totalProductosMenorIgualA5 = $productosAgrupados->count();
         return $totalProductosMenorIgualA5;
    

    }   
    


        }
    
    
    
