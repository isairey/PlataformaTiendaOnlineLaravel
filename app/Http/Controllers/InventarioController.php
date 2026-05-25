<?php

namespace App\Http\Controllers;
use App\Models\Gastos;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categorias;
use App\Models\Factura ;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductoImport;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function Inventario(){
        return view ('Administrador/Inventario');
    }

    public function registrar()
    {
        return view('Administrador/RegistrarProducto');
    }
    
    public function Agregar()
    {
        return view('Administrador/Agregar');
    }
    public function Ganancias()
    {
        return view('Administrador/Ventas');
    }


    public function obtenerProducto($id_productos)
    {
        $producto = Producto::find($id_productos);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    public function actualizarProducto(Request $request, $id_productos)
    {
        // Buscar el producto por su ID
        $producto = Producto::find($id_productos);
    
        // Verificar si el producto existe
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    
        // Validar los datos recibidos en el request
        $validacion = $request->validate([
            
            'nombre' => 'required|string|max:355',
            'referencia' => 'required|string|max:10000',
            'precio' => 'required|numeric|min:0',
            'fecha_de_vencimiento' => 'nullable|date',
        ], [
            'nombre.required' => 'La marca es obligatoria.',
            'referencia.required' => 'La referencia es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio debe ser un valor positivo.',
            'fecha_de_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.', 
        ]);
    

        $producto->nombre = $validacion['nombre'];
        $producto->referencia = $validacion['referencia'];
        $producto->precio = $validacion['precio'];
        $producto->fecha_de_vencimiento = $validacion['fecha_de_vencimiento'] ?? null; 
    
        // Guardar los cambios
        $producto->save();
    
        // Responder con éxito
        return response()->json(['message' => 'Producto actualizado correctamente'], 200);
    }
    
    public function listarinv(Request $request) {
        // Obtener productos con el nombre de la categoría mediante un join
        $inventarios = Producto::join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->select(
                'productos.*',
                'categorias.nombre AS nombre_categoria',
                  'categorias.id_categoria'
            )
            ->get();
      
        return $inventarios;
    }
    

    public function obtenerCategorias()
    {
        $categorias = Categorias::select('id_categoria', 'nombre')->distinct()->get();
        
        return response()->json($categorias);
    }

    public function referencias(){
        $referencias = Producto::select('referencia')->distinct()->get();
        return $referencias;
    }


    public function Registrarproducto(Request $request)
    {
        \Log::info('Datos recibidos:', $request->all());
    
        $validacion = $request->validate([
            'referencia' => 'required|string|max:255',
            'categoria' => 'required|exists:categorias,id_categoria',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precioC' => 'required|numeric|min:0',
            'fecha_de_vencimiento' => 'nullable|date',
            'cantidad' => 'nullable|integer|min:0',
            'unidad_peso' => 'nullable|string|max:50',
            'sePuedePesar' => 'required',
        ]);
    
        $categoria = Categorias::find($validacion['categoria']);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    
        if (!empty($validacion['unidad_peso']) && !empty($validacion['cantidad'])) {
            $pesoEnKilos = $validacion['cantidad'];
    
            switch ($validacion['unidad_peso']) {
                case 'g':
                    $pesoEnKilos = $pesoEnKilos / 1000;
                    break;
                case 'lb':
                    $pesoEnKilos = $pesoEnKilos * 0.453592;
                    break;
                case 't':
                    $pesoEnKilos = $pesoEnKilos * 1000;
                    break;
            }
    
            $validacion['cantidad'] = $pesoEnKilos;
        }
    
        $estado = isset($validacion['sePuedePesar']) && $validacion['sePuedePesar'] === true ? 1 : 0;
        
        $productoExistente = Producto::where('referencia', $validacion['referencia'])->first();
        if ($productoExistente) {
            return response()->json(['error' => 'El producto con esta referencia ya existe.'], 400);
        }
    
        $producto = new Producto([
            'referencia' => $validacion['referencia'],
            'id_categoria' => $categoria->id_categoria,
            'nombre' => $validacion['nombre'],
            'precio' => $validacion['precio'],
            'precioC' => $validacion['precioC'],
            'estado' => $estado,
            'fecha_de_vencimiento' => $validacion['fecha_de_vencimiento'] ?? null,
            'cantidad' => $validacion['cantidad'],
        ]);
    
        $producto->save();
    
        // Crear mensaje de gasto
        $mensaje = "Compro {$validacion['cantidad']} {$validacion['unidad_peso']} de {$validacion['nombre']}";
        $totalCosto = $validacion['cantidad'] * $validacion['precioC'];
    
        $Gasto = new Gastos();
        $Gasto->descripcion = $mensaje;
        $Gasto->precio = $totalCosto;
        $Gasto->save();
    
        return response()->json(['message' => 'Producto registrado correctamente y gasto registrado.'], 201);
    }
    
    
    public function Eliminar($id_productos)
    {
            $producto = Producto::find($id_productos);
    
            if (!$producto) {
                return response()->json([
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $producto->delete();

    
    }


    public function obtenerProductoPorReferencia(Request $request)
    {
        $busqueda = $request->input('busqueda');
    
        // Verificamos si el campo de búsqueda está vacío
        if (empty($busqueda)) {
            return response()->json([]);  // Devuelve una respuesta vacía si no hay búsqueda
        }
    
        $inventarios = Producto::join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->select(
                'productos.*',
                'categorias.nombre AS nombre_categoria',
                'categorias.id_categoria'
            )
            ->where('productos.referencia', '=', $busqueda) 
            ->get();
    
        return response()->json($inventarios);
    }
    
    
    public function cantidadProducto(Request $request)
    {
        $referencia = $request->input('referencia');
        $cantidad = Producto::where('referencia', $referencia)->value('cantidad');
    
    
            return response()->json(['cantidad' => $cantidad]);
    } 
        
    
    
    

// ProductoController.php

public function agregarProductos(Request $request)
{
    $validated = $request->validate([
        'producto' => 'required|array',
        'cantidad' => 'required|integer',
        'precioTotalDC' => 'required',
        'unidadPeso' => 'nullable|max:60',
    ]);

    try {

      ///7 función de gastos 
            $Descripcion = $validated['producto'] ?? null; // Asegura que existe
            $nombre = $Descripcion[0]['nombre'];

            $cantidad = $validated['cantidad'] ?? 0; // Valor por defecto si no existe
            $unidad = $validated['unidadPeso'] ?? 'unidad'; // Evita error si no existe

            $mensaje = "Compro $cantidad $unidad de $nombre";
            $PrecioGasto = isset($validated['precioTotalDC']) ? (int) preg_replace('/[^\d]/', '', $validated['precioTotalDC']) : 0;

            $Gastos  = new Gastos;
            $Gastos->descripcion = $mensaje;
            $Gastos->precio = $cantidad * $PrecioGasto; // Multiplica cantidad por precio

            $Gastos->save();





        $id = $validated['producto'];
        $producto = Producto::find($id);
        
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        

        $cantidad = $validated['cantidad'];
        $unidadPeso = $validated['unidadPeso'] ?? null;
        $precioIndividual = $request->precioID;
    

        if ($producto->isNotEmpty() && $producto->first()->estado == 0) {  // Verifica que haya productos
            $precioTotalDC = str_replace(['$', ' '], '', $validated['precioTotalDC']);
            $precioTotalDC = str_replace('.', '', $precioTotalDC);
            $precioTotal = intval($precioTotalDC);
        
            // Obtener el primer producto
            $producto = $producto->first();
        
            // Modificar los valores
            $producto->precioC = $precioTotal;
            $producto->cantidad += $cantidad;
            $producto->precio = $precioIndividual;
        
            // Guardar cambios en la base de datos
          
        
        }
         else {
    
            $precioT = isset($validated['precioTotalDC']) ? (int) preg_replace('/[^\d]/', '', $validated['precioTotalDC']) : 0;
           
            $unidadDePeso = $validated['unidadPeso'];
          
            switch ($unidadDePeso) {
                case 'gramos': $cantidad /= 1000; break;
                case 'miligramos': $cantidad /= 1000000; break;
                case 'libras': $cantidad *= 0.453592; break;
                case 'onzas': $cantidad *= 0.0283495; break;
                case 'toneladas': $cantidad *= 1000; break;
                case 'kilogramos': break;
                default:
                    throw new \Exception("Unidad de peso no válida para el producto");
            }
            $producto = $producto->first();
            $producto->cantidad += $cantidad;
            $producto->precioC = $precioT;
            $producto->precio = $precioIndividual;
            $producto->save();

      
        }
        
        $producto->save();
         ///Gatos 
       


        return response()->json([
            'message' => 'Productos agregados exitosamente',
            'producto' => $producto
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Error al agregar productos: ' . $e->getMessage());
        return response()->json(['message' => 'Error al agregar productos'], 500);
    }
}


/// funcones del mudlo de ventas

    public function Ventas(){
        return view ('Administrador/Ventas');
    }
    public function SacraVentas(Request $request)
    {
        // Número de elementos por página
        $perPage = $request->input('per_page', 3);

        // Obtener los datos paginados
        $facturas = Factura::paginate($perPage);

        // Transformar la descripción de cada factura en un array plano
        $resultados = [];
        foreach ($facturas as $factura) {
            $descripcion = json_decode($factura->descripcion, true);

            // Verificar que descripción sea un array válido y combinarlo en el resultado
            if (is_array($descripcion)) {
                $resultados = array_merge($resultados, $descripcion);
            }
        }

        // Responder con JSON paginado y datos planos
        return response()->json([
            'pagination' => [
                'total' => $facturas->total(),
                'current_page' => $facturas->currentPage(),
                'per_page' => $facturas->perPage(),
                'last_page' => $facturas->lastPage(),
                'from' => $facturas->firstItem(),
                'to' => $facturas->lastItem(),
            ],
            'resultados' => $resultados, // Array plano con todos los objetos de descripción
        ]);
    }
    

    


    public function gananciaTraer(Request $request)
    {
        try {
            // Obtener las fechas con validación
            $FechaInicial = $request->startDate ? \Carbon\Carbon::parse($request->startDate) : null;
            $FechaFinal = $request->endDate ? \Carbon\Carbon::parse($request->endDate) : null;
    
            // Verificar que las fechas sean correctas
            if ($FechaInicial && $FechaFinal && $FechaInicial->greaterThan($FechaFinal)) {
                return response()->json(['error' => 'La fecha inicial no puede ser mayor que la final'], 400);
            }
    
            // Obtener las facturas filtradas por fecha si se proporcionan
            $facturasQuery = Factura::query();
            if ($FechaInicial && $FechaFinal) {
                $facturasQuery->whereBetween('fecha_de_creacion', [$FechaInicial->startOfDay(), $FechaFinal->endOfDay()]);
            }
            $facturas = $facturasQuery->get();
    
            $resultados = [];
    
            // Procesar cada factura
            foreach ($facturas as $factura) {
                $descripcion = json_decode($factura->descripcion, true);
    
                // Verificar si la descripción es un array válido
                if (is_array($descripcion)) {
                    foreach ($descripcion as $item) {
                        if (!isset($item['referencia'])) {
                            continue; // Si no tiene referencia, ignoramos
                        }
    
                        // Buscar el producto por referencia
                        $producto = Producto::where('referencia', $item['referencia'])->first();
    
                        // Si el producto no existe, continuamos
                        if (!$producto) {
                            continue;
                        }
    
                        $cantidad = $item['cantidad'] ?? 0;
                        $PV =  $cantidad * ($producto->precio ?? 0);
                        $PC =  $cantidad * ($producto->precioC ?? 0);
                        $total = $PV - $PC;
    
                        // Agregar al resultado
                        $resultados[] = [
                            'nombre' => $producto->nombre ?? 'Desconocido',
                            'referencia' => $item['referencia'],
                            'cantidad_inventario' => $producto->cantidad ?? 0,
                            'cantidad_venta' => $cantidad,
                            'up' => $item['UP'] ?? null, // Evita error si no existe
                            'ganancias' =>  $total,
                        ];
                    }
                }
            }
    
            return response()->json([
                'status' => 'success',
                'ganancias' => $resultados
            ]);
    
        } catch (\Exception $e) {
            // Captura cualquier error inesperado y devuelve el mensaje
            return response()->json(['error' => 'Error interno del servidor', 'message' => $e->getMessage()], 500);
        }
    }
    

    }
    


    


