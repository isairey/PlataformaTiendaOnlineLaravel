<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Clientes;
use App\Models\Factura;
use App\Models\CuentasPendientes;
use App\Models\GananciasDiarias;
use App\Models\vendidos;
use App\Models\Categorias;
use Illuminate\Support\Facades\DB;
class FacturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function FacturaIndex (Request $request){
 
            return view ('Administrador/Factura');
    }
    /// scar categorias
    public function CategoriasSupermercado(Request $request){
        $Categorias = Categorias::all();
        return response()->json($Categorias); 
    }
    public function BusquedaCodigo(Request $request){
        //Busca referrencia del producto que seleciono 
        $codigo = $request->query('codigo');
        $producto = Producto::select('productos.*', 'categorias.nombre as categoria_nombre')
        ->join('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
        ->where('productos.referencia', $codigo)
        ->first();
    
        if ($producto) {
            preg_match('/\d+/', $producto->cantidad, $matches); // Extrae el número de la cantidad
            
            if (!empty($matches)) {
                // Si se encuentra una cantidad válida, la asignamos directamente al producto
                $producto->cantidad = (int) $matches[0];
            } else {
                // Si no se encuentra ninguna cantidad válida
                return response()->json(['message' => 'Cantidad no válida'],  400);
            }
        
            // Se devuelve el producto con la cantidad ya actualizada
            return response()->json($producto);
            } else {
                // Si no se encuentra el producto
                return response()->json(['message' => 'Producto no encontrado'],400 );
            }
        
    }
    public function GuardasClientes(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => ['required', 'regex:/^\d{6,10}$/'],
        ]);
    
        // Verificar si el cliente ya existe
        $clienteExistente = Clientes::where('documento', $validated['documento'])->first();
    
        if ($clienteExistente) {
            // Devolver respuesta con código de estado 409 para indicar conflicto
            return response()->json([
                'message' => 'Este cliente ya existe en la base de datos.',
            ], 409);
        }
    
        // Crear un nuevo cliente si no existe
        $usuario = new Clientes();
        $usuario->nombre = $validated['nombre'];
        $usuario->documento = $validated['documento'];
        $usuario->save();
    
        // Retornar respuesta exitosa
        return response()->json([
            'message' => 'Cliente creado exitosamente.',
        ]);
    }
    
    public function ListraUsuarios(Request $request)  // Lista usuarios y busca usuarios 
    {
        $usuario = $request->input('BusquedaDocumentoC');

        if ($usuario) {
            $clientes = Clientes::where('documento', 'like', "%{$usuario}%")->get();
        } else {
            $clientes = Clientes::all();
        }
        return response()->json($clientes);
    }
    public function RalizarFacturaBusqueda(Request $request){  /// Busqueda del usuaio completo por su documento para poder realizar su factura

        $documento = $request->documento; 
        $Datos = Clientes::where('documento',$documento)->get();
        return response()->json($Datos);

    }
   public function Factura(Request $request)
{
    DB::beginTransaction(); // Inicia la transacción

    try {
        $DatosClientes = $request->DatosClientes[0]; 
        $AgregarPruducto = $request->AgregarPruducto; 
        $Total = $request->Total; 
        $descuentoPorcentaje = $request->descuentoPorcentaje;
        $descuentoValor = $request->descuentoValor;
        $mp = $request->mp;
        $abono = $request->abono;

        $abono = preg_replace('/[^\d]/', '', $abono); 
        $abono = (int) $abono; 
        

        // Crear la factura
        $factura = new Factura();
        $factura->nombre = $DatosClientes['nombre'];
        $factura->documento = $DatosClientes['documento'];
        $factura->descripcion = json_encode($AgregarPruducto); // Guardar el JSON de productos
        $factura->mp = $mp;
        $factura->descuento = !empty($descuentoPorcentaje) ? $descuentoPorcentaje : (!empty($descuentoValor) ? $descuentoValor : 0);

        if ($mp == 3) {
            $factura->debe = $Total - $abono;
            $factura->totalF = $Total;
        } else {
            $factura->totalF = $Total;
        }
        $factura->save();

        // Guardar en CuentasPendientes
        if($mp == 3){
        $cuenta = new CuentasPendientes();
        $cuenta->documento = $DatosClientes['documento'];
        $cuenta->abono = $abono;
        $cuenta->save();
        }
        // Guardar en GananciasDiarias
     
            $GananciasD = new GananciasDiarias();
            $GananciasD->Total_G = $Total - $abono;
            $GananciasD->documento = $DatosClientes['documento'];
            $GananciasD->mp = $mp;
            $GananciasD->save();
     
       

        // Procesar y restar inventarios
        $resultado = [];
        foreach ($AgregarPruducto as $producto) {
            $idProducto = $producto['id_productos'];
            $unidadPeso = strtolower($producto['UP']);
            $cantidad = $producto['cantidad'];
            $estado = $producto['estado'];
            $precio = $producto['total'];

            // Conversión de unidades
            if ($estado == 1) {
                switch ($unidadPeso) {
                    case 'gramos': $cantidad /= 1000; break;
                    case 'miligramos': $cantidad /= 1000000; break;
                    case 'libras': $cantidad *= 0.453592; break;
                    case 'onzas': $cantidad *= 0.0283495; break;
                    case 'toneladas': $cantidad *= 1000; break;
                    case 'kilogramos': break;
                    default:
                        throw new \Exception("Unidad de peso no válida para el producto: $idProducto");
                }
            }

            $resultado[] = [
                'id_productos' => $idProducto,
                'cantidadConvertida' => $cantidad,
                'precio' => $precio
            ];
        }

        $id_productos = array_column($AgregarPruducto, 'id_productos');
        $productosDB = Producto::whereIn('id_productos', $id_productos)->get();

        foreach ($resultado as $producto) {
            $productoDB = $productosDB->firstWhere('id_productos', $producto['id_productos']);
            if ($productoDB) {
                // Validar si la cantidad restante sería negativa
                if ($productoDB->cantidad < $producto['cantidadConvertida']) {
                    if ($productoDB->cantidad < $producto['cantidadConvertida']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Error: No hay suficiente stock para el producto con ID {$producto['id_productos']}.",
                            'product_id' => $producto['id_productos']
                        ], 400);
                    }
                }
        
                // Realizar la resta
                $productoDB->cantidad -= $producto['cantidadConvertida'];
        
            
        
                // Guardar los cambios
                $productoDB->save();
            }
        }
        

        DB::commit(); // Confirma la transacción si todo sale bien
        return response()->json(['success' => true, 'message' => 'Factura procesada correctamente']);
    } catch (\Exception $e) {
        DB::rollBack(); // Revierte todos los cambios si ocurre un error
        return response()->json(['success' => false, 'message' => 'Error procesando la factura', 'error' => $e->getMessage()], 500);
    }
}       
    ///Guardar Nuevos productos nuevos 
    public function GuardarProductos(Request $request) {

        $unidadPeso = $request->unidadPeso;
        $Cantidad = $request->cantidadp;
    
        if (!empty($unidadPeso)) {
            switch (strtolower($unidadPeso)) {
                case 'gramos': // Gramos a kilogramos
                    $Cantidad = $Cantidad / 1000;
                    break;
                case 'miligramos': // Miligramos a kilogramos
                    $Cantidad = $Cantidad / 1000000;
                    break;
                case 'libras': // Libras a kilogramos (1 libra = 0.453592 kg)
                    $Cantidad = $Cantidad * 0.453592;
                    break;
                case 'onzas': // Onzas a kilogramos (1 onza = 0.0283495 kg)
                    $Cantidad = $Cantidad * 0.0283495;
                    break;
                case 'toneladas': // Toneladas a kilogramos (1 tonelada = 1000 kg)
                    $Cantidad = $Cantidad * 1000;
                    break;
                case 'kilogramos': // Ya está en kilogramos
                    // No es necesario hacer nada
                    break;
                default:
                    // Si la unidad de peso no es válida, lanzar un error o devolver una respuesta
                    return response()->json(['error' => 'Unidad de peso no válida'], 400);
            }
        }
    

        $Nombre = $request->nombrep;
        $Referencia = $request->referenciap;
        $preciototal = $request->preciototal;
        $preciounidad = $request->preciounidad;
        $preciototalC = $request->precioTotalC;
        $preciounidadC = $request->precioUnitarioC;
        $categoriap = $request->categoriap;
        $esPesa = $request->esPesa;
    
        $Productos = new Producto();
        $Productos->nombre = $Nombre;
        $Productos->referencia = $Referencia;
        $Productos->cantidad = $Cantidad;
        if ($esPesa == 'Sí') {
            $Productos->precio = $preciototal;
            
            $Productos->precioC = $preciototalC;
        }else{
            $Productos->precio = $preciounidad; 
            $Productos->precioC = $preciounidadC; 
        }
        $Productos->id_categoria = $categoriap;
        if ($esPesa == 'Sí') {
            $Productos->estado = 1;
        }
        $Productos->save();
        return response()->json(['success' => 'Producto guardado correctamente']);
    }
    /// Sacar Categorias Supermercado
    public function SacarOrdenServicio(){
     
        $orden = Factura::orderBy('orden_servicio', 'desc')->first();
        $ultimaOrden = $orden ? $orden->orden_servicio + 1 : 1; 

        return $ultimaOrden;
    }
    
    
}
