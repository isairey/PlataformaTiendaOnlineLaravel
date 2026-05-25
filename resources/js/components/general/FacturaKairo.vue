<template>
<br>
<div id="cabeza">
    <div id="Cuerpo">
        <div class="Busqueda">
            <div class="input-group mb-6">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="re">
                         <button style="border: none; background: none;"  >
                        <i class="fas fa-search" ></i>
                        </button>
                    </span>
                </div>
                  <input type="text" class="form-control" id="buscar1111"  @keyup.enter="AgregarProducto"  placeholder="Buscar..." aria-label="Buscar" v-model="BucarReferencia" aria-describedby="search-icon" autocomplete="off">
            </div>    
            <div class="ActivarCodigo">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activarCodigo">
                    <label class="form-check-label" for="activarCodigo">Código de Barras</label>
                </div>
            </div>
        </div>

        <div id="BotonBusqueda">
            <button class="btn-enviar" @click="AgregarProducto">
                <i class="fas fa-shopping-bag"></i> Agregar a la factura
            </button>

            <button class="btn-enviar" id="boton22" data-toggle="modal" data-target="#modalFactura" @click="CategoriasListar">
                <i class="fas fa-shopping-cart"></i> Añadir a Factura
            </button>
        </div>
        <br>
        <div class="table-responsive mailbox-messages">
              <div style="overflow-x: auto;">
                <table  class="table table-hover table-striped" id="factura-para-imprimir">
                    <thead>
                        <tr>
                        <th>Nombre</th>
                        <th>Referencia</th>
                        <th>Cantidad</th>
                        <th>UP</th>
                        <th>Precio</th>
                        <th>Categoria</th>
                        <th class="eliminar-th">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                         <tr v-for="Producto in AgregarPruducto" :key="Producto.id_productos">
                            <td class="col-1 text-center eliminar-td">{{ Producto.nombre }}</td>
                            <td class="col-1">{{ Producto.referencia }}</td>
                                <td class="col-1">
                                    <input 
                                        type="text" id="BotonDeCantidad" v-model="Producto.cantidad" @input="calcularTotal(Producto)"  autocomplete="off" />
                                </td>
                                <td class="col-1" v-if="Producto.estado == 1">
                                    <select id="unitOfWeight" v-model="Producto.UP" required @change="calcularTotal(Producto)">
                                        <option value=""  selected>N/A</option>
                                        <option value="kilogramos">Kilogramos (kg)</option>
                                        <option value="gramos">Gramos (g)</option>
                                        <option value="miligramos">Miligramos (mg)</option>
                                        <option value="libras">Libras (lb)</option>
                                        <option value="onzas">Onzas (oz)</option>
                                        <option value="toneladas">Toneladas (t)</option>
                                    </select>
                                </td>
                                <td class="col-1" v-if="Producto.estado == 0">
                                    <select id="unitOfWeight">
                                        <option value=""  selected>UNIDAD</option>
          
                                    </select>
                                </td>
                                <td class="col-1">
                                    <input
                                       type="text" id="BotonDePrecio" v-model="Producto.total" @input="SacarTotal(Producto)"  autocomplete="off" />
                                </td>
                            <td class="col-1">{{ Producto.categoria_nombre }}</td>
                           <td class="col-1 text-center eliminar-td">
                                <button class="btn btn-danger" @click="EliminarProducto(Producto.referencia)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    
                        <div class="total-row" id="TotalDefacturatabla" v-if="MostrasTotalF === 1">
                            <tr>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111">Descuento</td>
                            <td class="tabla1112" v-if="descuentoPorcentaje !== null && descuentoPorcentaje > 0">
                                {{ formatCurrency(descuentoPorcentaje) }}
                            </td>
                            <td class="tabla1112" v-else-if="descuentoValor !== null && descuentoValor > 0">
                                {{ formatCurrency(descuentoValor) }}
                            </td>
                            <td class="tabla1112" v-else>
                                $0
                            </td>


                            </tr>
                            <tr>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111">Total</td>
                            <td class="tabla1112">{{ formatCurrency(total) }}</td>
                            </tr>

                        </div>
                        
                    </tbody>
                </table>
               
            </div>
         </div>
         <hr>
        <div id="piedepagina">
           <div id="BotonBusquedaEliminar">
            <button class="btn btn-danger" @click="VaciarLista">
                <i class="fas fa-trash"></i> Cancelar Factura
            </button>
            <button class="btn btn-dark"  @click="Modal2">
                <i class="fas fa-percent"></i> Descuento
            </button>
       
          <div class="btn-group">
                <select class="form-select btn btn-dark" style="width: auto;" v-model="mp">
                    <option value="" disabled selected>Métodos de pago</option>
                    <option value="1">Pago en Efectivo</option>
                    <option value="2">Pago en transferencia</option>
                    <option value="3">Crédito</option>
                </select>
            </div>
            <input v-if="mp == 3" type="text" id="abono" class="form-control" v-model="abono" @input="formatearAbono" placeholder="Ingrese abono" autocomplete="off">
          </div> 

         <div id="TotalF">
            <div id="moverTotal">
            <p>Total <span class="amount">{{ formatCurrency(total)  }}</span></p>
            </div>
        </div>
    </div>

    </div>

<div id="cuerpo2">
    <div id="CuerpoClinetes">
        <h4 class="titulo"> Datos de Venta <i class="fas fa-shopping-cart"></i></h4>
        <hr class="hr">
    <div id="ClientesC">
        <h6 style="font-weight: bold; font-size: 1.1rem;" class="Clientes242411111">Cliente</h6>
        <div class="input-icon-container">
            <input type="text" id="inpudClientes" readonly :value="getNombre()"> 
            <button class="btn-icon" id="icnocliente" data-toggle="modal" data-target="#exampleModal" @click="MostraUsuarios">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <br>
        <div id="containesDeClientes">
            <div class="client-info">
                <h6 class="Clientes2424">Comprobante</h6>
                <input type="text" id="inpudClientes1" readonly value="Ticket">
            </div>
            <div class="client-info">
                <h6 class="Clientes2424">Orden de servicio</h6>
                <input type="text" id="inpudClientes2"  v-model="orden" readonly>
            </div>
        </div>
        </div>
    </div>

    <div id="Calcular">
        <h4 class="titulo">Realiza venta <i class="fas fa-cart-plus"></i> </h4>
        <hr class="hr">
        <div id="ClientesC">
        <div class="input-icon-container" id="TotalDeVentas">
            <input type="text" id="inpudClientes" :value="formatCurrency(total)" readonly style="text-align: center; font-weight: bold;" />
        </div>
        <br>
        <div id="containesDeClientes">
            <div class="client-info11">
                <h6 class="Clientes24241">Cantidad</h6>
                <input type="text" id="InpudDeVentas" v-model="CantidadVenta"  @input="formatearPrecioVenta" autocomplete="off">
            </div>
            <div class="client-info">
                <h6 class="Clientes2424122">Cambio</h6>
                <input type="text" id="InpudDeVentas1" readonly    :value="formatCurrency(TOTALDEPAGO)"   >
            </div>
        </div>

        <div class="button-container">
            <button class="btn-blue" id="calcular1" @click="SacarCambio">
                <i class="fas fa-check"></i> Calcular
            </button>
            <button class="btn" id="idfa" @click="Fctura">
                <i class="fas fa-check"></i> Realiza factura
            </button>
            
        </div>

        </div>
    </div>
</div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" id="MODAL1">
        <div class="modal-content" id="ModalCliente">
            <div class="modal-header">
                <button type="button" class="btn btn-primary d-flex align-items-center" style="font-size: 1rem; background-color: #2e7d32; border-color: #2e7d32;" @click="openModal">
                    <i class="fas fa-user-plus" style="margin-right: 8px;"></i> Agregar Cliente
                </button>
               <button type="button" class="btn btn-successd-flex align-items-center" style="font-size: 1rem;" @click="openModal2" v-if="Modal === 1">
                   <i class="fas fa-search" style="color: #2e7d32; font-size: 1.6rem; margin-right: 10px;"></i>
                </button>
                 <div class="input-group"  id="InpuDeBusqueda" v-if="Modal === 0" >
                    <input type="text" class="form-control"  placeholder="Buscar cliente por documento" aria-label="Buscar cliente por documento" v-model="BusquedaDocumentoC"  />
                <button  @click="MostraUsuarios" id="Bontondebusqueda" style="background: none; ">
                    <span class="input-group-text1">
                        <i class="fas fa-search"></i>
                    </span>
                </button>   
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="x">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <div class="modal-body">

            <div v-if="Modal === 0" class="modal-container">
                <table class="table" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th scope="col">Op</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Documento</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">    
                        <tr v-for="Usuario in Usuarios" :key="Usuario.id_cliente">
                            <th  >
                                <button class="btn btn" @click="RalizarFactura(Usuario.documento)" id="opclientes">
                                    <i class="fas fa-file-invoice"></i>
                                </button>
                            </th>
                            <td>{{ Usuario.nombre }}</td>
                            <td>{{ Usuario.documento }}</td>
                        </tr>
                    </tbody>
                </table>

                     <div v-if="noClienteFound" class="alert alert-warning mt-3 custom-alert">
                      No se encontró el documento del cliente.
                    </div>
            </div>

            <div v-if="Modal === 1" id="conten">
             <form @submit.prevent="submitForm">
                <div>
                    <label for="nombre">Nombre del Cliente</label>
                    <input type="text" id="nombre" v-model="nombrec"  placeholder="Ingresa el nombre del cliente"  autocomplete="off" />
                </div>
                <div>
                    <label for="documento">Documento</label>
                    <input type="text" id="documento" v-model="documento"  placeholder="Ingresa el documento"  autocomplete="off"/>
                </div>

               <button type="submit" class="btn-success" id="botonguardardatos" @click="GuardarDatosClientes">Guardar y Generar Factura</button>
            </form>
  
         </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="descuentoModal" tabindex="-1" aria-labelledby="descuentoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm"> <!-- Puedes usar modal-sm para un modal pequeño -->
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title d-flex align-items-center" id="descuentoModalLabel">
          <i class="fas fa-percent me-2"></i> Descuento
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <!-- Descuento por porcentaje -->
            <div class="input-container d-flex align-items-center"  id="c1">
                <div class="input-group" id="ii">
                <i class="fas fa-percentage"></i>
                <input type="text" class="custom-input" id="input1"  placeholder="Ingrese porcentaje" v-model="descuento"/>
            </div>
            </div>
            <!-- Descuento personalizado -->
            <div class="input-container d-flex align-items-center" id="c2">
                <div class="input-group" id="i">
                    <i class="fas fa-tag fa-2x"></i> 
                    <input type="text" class="custom-input" id="input2" placeholder="Ingrese descuento" v-model="descuentoper" />
                </div>
            </div>

        </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-dark" @click="RealizarDescuento">Realizar Descuento</button>
      </div>
    </div>
  </div>
</div>
 <!-- //modal paera registra porductos -->
<div class="modal fade" id="modalFactura" tabindex="-1" role="dialog" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFacturaLabel">Añadir a la Factura</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="x">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
            <form id="modaldeproducto">
                <div class="mb-3">
                <label for="productName" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="productName" placeholder="Ej: Manzana Roja" v-model="nombrep" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="productReference" class="form-label">Referencia</label>
                <input  class="form-control" id="productReference" placeholder="Ej: FRU1234"  v-model="referenciap" required autocomplete="off">
            </div>
            <div class="mb-3" id="FlexProducto">
                <div class="form-group">
                    <label for="productQuantity1" class="form-label">Cantidad</label>
                    <input type="text" class="form-control" id="productQuantity1" placeholder="Ej: 5" v-model="cantidadp" min="1" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="unitOfWeight" class="form-label">Escoge unidad de peso</label>
                    <select  id="unitOfWeight" v-model="unidadPeso"   @change="ActualizarUnidadPeso(index)"  required>
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="kilogramos">Kilogramos (kg)</option>
                        <option value="gramos">Gramos (g)</option>
                        <option value="miligramos">Miligramos (mg)</option>
                        <option value="libras">Libras (lb)</option>
                        <option value="onzas">Onzas (oz)</option>
                        <option value="toneladas">Toneladas (t)</option>
                            
                    </select>
                </div>

            </div>
            <div class="mb-3">
                <label for="categorySelect" class="form-label">Selecciona una categoría</label>
                <select 
                    id="categorySelect"
                    class="form-control custom-select" v-model="categoriap">
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option 
                    v-for="Categoria in Categorias" 
                    :key="Categoria.id_categoria" 
                    :value="Categoria.id_categoria">
                    {{ Categoria.nombre }}
                    </option>
                </select>
                </div>
                <div class="mb-3" id="preguntadepeso">
                    <label class="form-label">¿El producto se pesa?</label>
                    <div >
                        <div style="display: flex;  gap: 20px;" id="sepesa">
                            <div>
                                <input type="radio" id="productWeightYes" value="Sí" v-model="esPesa" />
                                <label for="productWeightYes" id="sipesa">Sí</label>
                            </div>
                            <div>
                                <input type="radio" id="productWeightNo" value="No" v-model="esPesa" />
                                <label for="productWeightNo" id="nopesa">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3" v-if="esPesa === 'Sí'">
                    <label for="productPrice" class="form-label">Precio de venta(1kg)</label>
                    <input type="text" class="form-control" id="productPrice" v-model="precioTotal" autocomplete="off" @input="formatearPrecio" >
                </div>
                <div class="mb-3" v-if="esPesa === 'Sí'">
                    <label for="productPrice" class="form-label">Precio total de compra</label>
                    <input type="text" class="form-control" id="productPrice" v-model="precioTotalC" autocomplete="off" @input="formatearPrecio2" >
                </div>
                <div class="mb-3" v-if="esPesa === 'No'">
                    <label for="productPrice" class="form-label">Precio de Venta</label>
                    <input type="text" class="form-control" id="productPrice"  v-model="precioUnitario" @input="formatearPrecio1" autocomplete="off">
                </div>
                <div class="mb-3" v-if="esPesa === 'No'">
                    <label for="productPrice" class="form-label">Precio total de Compra</label>
                    <input type="text" class="form-control" id="productPrice"  v-model="precioUnitarioC" @input="formatearPrecio3" autocomplete="off">
                </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn"  id="botonproducto" @click="GuardarProductos">Confirmar</button>
      </div>
    </div>
  </div>
</div>


</template>
<script>
import axios from 'axios';
import Swal from 'sweetalert2';
export default {

  data() {
    return {
    Modal: 0,
    precioTotalC: '',
    precioUnitarioC: '',
    orden: null,
    estado: 0,
    MostrasTotalF: 0,
    BucarReferencia: '',
    Productos: [],
    AgregarPruducto: [],
    total:'',
    CantidadVenta: 0, 
    TOTALDEPAGO: 0,
    nombre: 'Keiner Reyes',
    nombrec:'',
    documento: '',
    Usuarios:[],
    BusquedaDocumentoC:'',
    DatosClientes:[],
    DatosClientesNuevos:[],
    noClienteFound: false,
    totalFormatted: 0,
    EstadoConSIste: 0,
    descuento: '',
    descuentoper: '',
    descuentoPorcentaje: null, 
    descuentoValor: null,
    nombrep: '',
    referenciap: '',
    cantidadp:'',
    preciop: '',
    categoriap: '',
    marcap:'',
    CantidadProducto: 0,
    Categorias:[],
    precioTotal: '',
    mp: 1,
    TotalPorducto: '',
    abono: 0,
    unidadPeso: '',
    esPesa:'',
    precioUnitario: '',
    UnidadPesoF: '',
    };
     },
    methods: {
    formatCurrency(value) {
      if (value === '') return '';
      const number = parseFloat(value);
      return new Intl.NumberFormat('es-CO', { 
        style: 'currency', 
        currency: 'COP', 
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
      }).format(number);
    },
    formatearPrecio() {
      let precioFormateado = this.precioTotal.replace(/\D/g, '');
      precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      precioFormateado = '$ ' + precioFormateado;
      this.precioTotal = precioFormateado;
    },
    formatearPrecio2(){
        let precioFormateado = this.precioTotalC.replace(/\D/g, '');
      precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      precioFormateado = '$ ' + precioFormateado;
      this.precioTotalC = precioFormateado;
    },
    formatearPrecio3(){
    let precioFormateado = this.precioUnitarioC.replace(/\D/g, '');
    precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    precioFormateado = '$ ' + precioFormateado;
    this.precioUnitarioC = precioFormateado;
    },
    formatearPrecio1() {
      let precioFormateado = this.precioUnitario.replace(/\D/g, '');
      precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      precioFormateado = '$ ' + precioFormateado;
      this.precioUnitario = precioFormateado;
    },
    Modal2() {
        const modalElement = document.getElementById('descuentoModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    },
    openModal() {
        this.Modal = 1;
    },
    openModal2(){
        this.Modal = 0;
    },
    BuscarReferenciaProducto() {
    return axios.get('/BusquedaF', {
        params: {
        codigo: this.BucarReferencia
        }
    })
    .then(response => {
        this.SacarOrdenServicio();
        if (Array.isArray(response.data)) {
        return response.data;
        } else if (response.data && Object.keys(response.data).length === 0) {
        return []; 
        } else {
        return [response.data];
        }
      
    })
    .catch(error => {
        throw error;
    });
    },
    async AgregarProducto() {
    if (this.BucarReferencia) {
        try {
            const productosEncontrados = await this.BuscarReferenciaProducto();
            if (productosEncontrados.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto no encontrado',
                    text: `No se encontró ningún producto con la referencia ${this.BucarReferencia}.`,
                });
            } else {
                productosEncontrados.forEach(producto => {
                    if (producto.cantidad <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cantidad insuficiente',
                            text: `El producto con la referencia ${producto.referencia} tiene una cantidad insuficiente (0 o menor).`,
                        });
                        return;
                    }

                    const productoYaEnLista = this.AgregarPruducto.some(p => p.referencia === producto.referencia);

                    if (productoYaEnLista) {
                        const index = this.AgregarPruducto.findIndex(p => p.referencia === producto.referencia);
                        this.AgregarPruducto[index].precio = producto.estado === 1 ? 0 : producto.precio || 0;
                    } else {
                        this.AgregarPruducto.push({
                            ...producto,
                            cantidad: '',  // Campo inicial vacío
                            precio:  producto.precio ,
                            precioVenta:  producto.precio ,
                            total: '',  // Total inicial vacío
                            estado: producto.estado,
                            UP: producto.UP || '',
                        });
                    }
                });
                this.BucarReferencia = '';
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al intentar agregar el producto. Por favor, intenta nuevamente.',
            });
        }
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Referencia vacía',
            text: 'Por favor, ingresa una referencia antes de buscar.',
        });
    }
    },
    calcularTotal(producto) { 
    console.log(producto);

    if (producto.estado === 1) {
        this.calcularTotalPorPeso(producto);
    } else {
        this.calcularTotalPorUnidad(producto);
    }

    this.SacarTotal();
},

    calcularTotalPorPeso(producto) {
        if (!producto.cantidad || !producto.UP) {
            console.log('Faltan la cantidad o la unidad de peso');
            return; // No hacer nada si falta alguno de los valores
        }

        let cantidadConvertida = producto.cantidad;

        switch (producto.UP) {
            case 'gramos':
                cantidadConvertida /= 1000;
                break;
            case 'miligramos':
                cantidadConvertida /= 1000000;
                break;
            case 'libras':
                cantidadConvertida *= 0.453592;
                break;
            case 'onzas':
                cantidadConvertida *= 0.0283495;
                break;
            case 'toneladas':
                cantidadConvertida *= 1000;
                break;
            case 'kilogramos':
                break;
            default:
                console.log('Unidad de peso no válida');
                return; // Si la unidad no es válida, no hace nada
        }

        const precio = parseFloat(producto.precio) || 0;
        producto.total = Math.round(cantidadConvertida * precio);
    },

    calcularTotalPorUnidad(producto) {
        
        const cantidad = parseFloat(producto.cantidad) || 0;
        const precio = parseFloat(producto.precio) || 0;
        producto.total = cantidad * precio;
    },
    obtenerFechaEnFormato() {
        const ahora = new Date();
        const opciones = { day: 'numeric', month: 'long' };
        const formatoFecha = new Intl.DateTimeFormat('es-ES', opciones).format(ahora);
        return formatoFecha;
    },
    EliminarProducto(referencia) {

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-danger btn-margin',
            cancelButton: 'btn btn-secondary btn-margin'
        },
        buttonsStyling: false
        });


        swalWithBootstrapButtons.fire({
        title: 'Eliminar Producto',
        text: 'Esta acción eliminará permanentemente el producto seleccionado. ¿Deseas continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
        }).then((result) => {
        if (result.isConfirmed) {
        const index = this.AgregarPruducto.findIndex(producto => producto.referencia === referencia);
        
        if (index !== -1) {
            this.AgregarPruducto.splice(index, 1);

            swalWithBootstrapButtons.fire({
            title: 'Eliminación Exitosa',
            text: 'El producto seleccionado ha sido eliminado correctamente.',
            icon: 'success'
            });
            this.SacarTotal();

        } else {
            swalWithBootstrapButtons.fire({
            title: 'Producto no encontrado',
            text: 'No se encontró el producto con el código proporcionado en la lista.',
            icon: 'error'
            });
        }
        }
    });
    },
    VaciarLista() {
      

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-danger btn-margin',
            cancelButton: 'btn btn-secondary btn-margin'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'Vaciar Lista de Productos',
            text: 'Esta acción eliminará todos los productos de la lista. ¿Deseas continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Vaciar Lista',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
            this.AgregarPruducto = [];
            this.SacarTotal();
            swalWithBootstrapButtons.fire({
                title: 'Lista Vacía',
                text: 'Todos los productos han sido eliminados de la lista.',
                icon: 'success'
            });

         
            }
        });
    },
    SacarTotal() {
        this.total = this.AgregarPruducto.reduce((acumulado, producto) => {
            const totalProducto = parseFloat(producto.total) || 0;
            return acumulado + totalProducto;
        }, 0);
    },
     formatearAbono() {
      let precioFormateado = this.abono.replace(/\D/g, '');
      precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      precioFormateado = '$ ' + precioFormateado;
      this.abono = precioFormateado;
    },  
    formatPrecio(precio) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(precio);
    },
    formatCurrency(value) {
            return new Intl.NumberFormat('es-CO', { 
                style: 'currency', 
                currency: 'COP', 
                minimumFractionDigits: 0, 
                maximumFractionDigits: 0 
            }).format(value);
    },
    formatearPrecioVenta() {
        let precioFormateado = String(this.CantidadVenta);  // Aseguramos que es una cadena
        precioFormateado = precioFormateado.replace(/\D/g, ''); // Elimina caracteres no numéricos
        if (precioFormateado.length > 3) {
            precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Agrega puntos de miles
        }
        this.CantidadVenta = precioFormateado ? '$ ' + precioFormateado : ''; // Agrega el signo de pesos
    },
    SacarCambio() {
           const cantidadString = this.CantidadVenta.replace(' $', '').replace('$', '').trim();
           const cantidad = parseFloat(cantidadString) * 1000;
            const total = parseFloat(this.total);
            this.TOTALDEPAGO = cantidad - total;
             if (this.TOTALDEPAGO > 0) {
                this.estado = 1; 
            } else {
                this.estado = 0; 
            }
    },
    GuardarDatosClientes() {
    axios.post('GuardasClientes', {
        'nombre': this.nombrec,
        'documento': this.documento
    })
    .then(response => {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Cliente guardado correctamente.'
        });
        this.RealizaFacturaCreacionClientes();
        this.nombrec = '';
        this.documento = '';
    })
    .catch(error => {
        // Verificar si el error es un conflicto (409) o algún otro tipo de error
        const mensaje = error.response?.data?.message || 'Ocurrió un error.';
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });

        const errors = error.response?.data?.errors;
        if (errors) {
            if (errors.documento) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Documento',
                    text: errors.documento[0] // Mostrar el error específico si está disponible
                });
            }
        }
    });
    },
    MostraUsuarios() {
        axios.get('/ListraUsuarios', {
        params: {
            BusquedaDocumentoC: this.BusquedaDocumentoC
        }
        })
        .then((response) => {
        this.Usuarios = response.data;
        if (this.Usuarios.length > 0) {
            this.noClienteFound = false; 
        } else {
            Swal.fire({
            icon: 'error',
            title: 'Cliente no encontrado',
            text: `No se encontró un cliente con el documento ${this.BusquedaDocumentoC}.`,
            confirmButtonText: 'Aceptar'
            });
            this.noClienteFound = true; 
        }
        })
        .catch((error) => {
   
        });
    },
    RalizarFactura(documento) {
        let me = this;
        axios.get('/RalizarFacturaBusqueda', {
            params: {
                documento: documento
            }
        })
        .then(function (response) {
            me.DatosClientes = response.data;
            
        })
        .catch(function (error) {
        
        });
    },
    RealizaFacturaCreacionClientes(){
     let me = this;
      axios.get('/RalizarFacturaBusqueda', {
            params: {
                documento: this.documento
            }
        })
        .then(function (response) {
            me.DatosClientes = response.data;
            
        })
        .catch(function (error) {
       
        });
    },
    getNombre() {
    return this.DatosClientes.length > 0 ? this.DatosClientes[0].nombre : 'Venta Rápida';
    },
    /// imprimir factura !!! 
    imprimirConIframe(titulo, DatosClientes) {
    function obtenerFechaActual() {
        const fecha = new Date();
        const dia = String(fecha.getDate()).padStart(2, '0');
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const año = fecha.getFullYear();
        return `${dia}/${mes}/${año}`;
    }

    function obtenerUnidadMedida(unidad) {
        const unidades = {
            'kilogramos': 'kg',
            'gramos': 'g',
            'miligramos': 'mg',
            'libras': 'lb',
            'onzas': 'oz',
            'toneladas': 't'
        };
        return unidades[unidad] || ''; // Retorna vacío si la unidad no existe
    }

    if (!this.DatosClientes || Object.keys(this.DatosClientes).length === 0 || Object.values(this.DatosClientes).every(value => value === null || value === '' || value === undefined)) {
        this.DatosClientes = [
            {
                id_cliente: 1,
                nombre: 'Keiner Reyes',
                documento: 1065884332,
                fecha_de_creacion: '2024-11-22 15:12:34'
            }
        ];
    }
    const orden = this.orden;
    const total = this.total;
    let totalFormateado = total.toLocaleString('es-CO', { 
        style: 'currency', 
        currency: 'COP', 
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
    }).replace("COP", "").trim();

    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);

    let contenidoHTML = '';
    const doc = iframe.contentWindow.document;

    // Generar contenido para los clientes
    this.DatosClientes.forEach(cliente => {
        contenidoHTML += `
            <div class="cliente">
                <p><strong>Nombre:</strong> ${cliente.nombre}</p>
                <p><strong>Documento:</strong> ${cliente.documento}</p>
                <p><strong>Orden de Servicios:</strong> ${orden}</p>
                <p><strong>Fecha de Admisión:</strong> ${obtenerFechaActual()}</p>
                <div class="linea"></div>
            </div>
        `;
    });

    // Generar contenido para los productos
    const AgregarProducto = this.AgregarPruducto; // Asegúrate de que está bien definido
    let contenidoProductos = '';
    AgregarProducto.forEach(producto => {
        const nombre = producto.nombre || 'Sin nombre';
        const cantidad = producto.cantidad || 0;
        const total = Number(producto.total) || 0.00; // Asegurar que total sea un número
        const unidadAbreviada = obtenerUnidadMedida(producto.UP); // Obtener la abreviatura usando UP

        // Validación: si la unidad abreviada está vacía, solo muestra la cantidad
        const cantidadConUnidad = unidadAbreviada ? `${cantidad} ${unidadAbreviada}` : `${cantidad}`;

        contenidoProductos += `
            <div class="producto">
                <div class="nombre-cantidad">${nombre} (${cantidadConUnidad})</div>
                <div class="precio">$${total.toFixed(0)}</div>
            </div>
        `;
    });

    // Escribir en el iframe
    doc.open();
    doc.write(`
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 10px;
                    background-color: #f9f9f9;
                    color: #333;
                    width: 58mm; /* Ancho de la factura */
                    font-size: 13px; /* Ajustar el tamaño de la fuente */
                }
                h1 {
                    text-align: center;
                    color: #333;
                    font-size: 16px;
                }
                .header {
                    margin-bottom: 10px;
                }
                .header p {
                    font-size: 13px;
                    margin: 5px 0;
                }
                .linea {
                    border-bottom: 1px solid #bbb;
                    margin: 10px 0;
                }
                .producto {
                    display: flex;
                    justify-content: space-between;
                    padding: 5px 0;
                    
                    margin-right:10px;
                }
                .nombre-cantidad {
                    flex: 1;
                }
                .precio {
                    text-align: right;
                    width: 60px;
                }
                .total {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    font-weight: bold;
              
                    margin-right:20px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                ${contenidoHTML}
            </div>

            <div class="contenido">
                ${contenidoProductos}
            </div>

            <div class="linea"></div>
            <div class="total">
                <div><strong>Total:</strong></div>
                 <div>${totalFormateado}</div>
            </div>
        </body>
        </html>
    `);
    doc.close();

    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    // Remover el iframe después de la impresión
    document.body.removeChild(iframe);
},



    /////factura
    Fctura() {
    // Verificación de productos
    if (!this.AgregarPruducto || this.AgregarPruducto.length === 0) {
        Swal.fire({
            title: "Error",
            text: "Debe agregar al menos un producto.",
            icon: "error"
        });
        return;
    }

    // Verificación de datos del cliente
    if (
        !this.DatosClientes || 
        Object.keys(this.DatosClientes).length === 0 || 
        Object.values(this.DatosClientes).every(value => value === null || value === '' || value === undefined)
    ) {
        // Si no hay datos del cliente, establecer un cliente predeterminado
        this.DatosClientes = [
            {
                id_cliente: 1,
                nombre: 'Keiner Reyes',
                documento: 1065884332,
                fecha_de_creacion: '2024-11-22 15:12:34'
            }
        ];
    }

    // Verificación del total
    if (!this.total || this.total <= 0) {
        Swal.fire({
            title: "Error",
            text: "El total debe ser mayor a cero.",
            icon: "error"
        });
        return;
    }

    // Verificación del método de pago
    if (!this.mp) {
        Swal.fire({
            title: "Error",
            text: "Por favor, selecciona un método de pago válido.",
            icon: "error"
        });
        return;
    }

    // Enviar solicitud POST para guardar la factura
    axios.post('/Factura', {
        DatosClientes: this.DatosClientes,
        AgregarPruducto: this.AgregarPruducto,
        Total: this.total,
        descuentoPorcentaje: this.descuentoPorcentaje, 
        descuentoValor: this.descuentoValor,
        mp: this.mp,
        abono: this.abono,
        UnidadPesoF: this.UnidadPesoF
    })
    .then((response) => {
        // Asignar los usuarios de la respuesta (si es necesario)
        this.Usuarios = response.data;

        Swal.fire({
        html: `<p>¡La factura se ha guardado correctamente! ¿Desea imprimirla o solo guardarla?</p>`,
        icon: "success",
        showCancelButton: true,
        confirmButtonText: 'Imprimir',
        cancelButtonText: 'Solo guardar',
        customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton: 'swal2-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Si se confirma, imprime y reinicia la página
            this.imprimirConIframe(); // Asegúrate de que esta función esté bien implementada
            window.location.reload(); // Reinicia la página
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Si se cancela, solo guarda y reinicia la página
           
            window.location.reload(); // Reinicia la página
        }
    });

    }).catch((error) => {
        if (error.response && error.response.data) {
            const responseData = error.response.data;

            // Obtener el mensaje de error
            const errorMessage = responseData.message || "Error desconocido.";

            // Mostrar el error con Swal.fire
            Swal.fire({
                title: "Error al Guardar",
                text: errorMessage,
                icon: "error"
            });
        } else {
            // Mostrar un error genérico si no hay respuesta del servidor
            Swal.fire({
                title: "Error al Guardar",
                text: "Hubo un problema al guardar la factura. Inténtelo de nuevo más tarde.",
                icon: "error"
            });
        }
    });
    },
    formatearPrecioDescuento() {
    let precioFormateado = String(this.descuentoper);  // Aseguramos que es una cadena
    precioFormateado = precioFormateado.replace(/\D/g, ''); // Elimina caracteres no numéricos
    if (precioFormateado.length > 3) {
        precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Agrega puntos de miles
    }
    this.descuentoper = precioFormateado ? '$ ' + precioFormateado : ''; // Agrega el signo de pesos
    },
    RealizarDescuento() {

        const isDescuentoFilled = this.descuento.trim() !== '';
        const isDescuentoPerFilled = this.descuentoper.trim() !== '';

        if (isDescuentoFilled && isDescuentoPerFilled) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Solo uno de los campos debe estar lleno, no ambos.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir de la función si ambos campos están llenos
        }

        const isDescuentoValid = /^\d*\.?\d+$/.test(this.descuento);
        const isDescuentoPerValid = /^\d*\.?\d+$/.test(this.descuentoper);

        if ((!isDescuentoFilled && !isDescuentoPerFilled) || (!isDescuentoValid && !isDescuentoPerValid)) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Uno de los campos debe estar lleno y contener solo números.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir de la función si las validaciones no se cumplen
        }

        // Mostrar alerta de confirmación
        Swal.fire({
            title: 'Aplicar Descuento',
            text: '¿Está seguro de que desea aplicar el descuento?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (this.descuento) {
                    let descuento = parseFloat(this.descuento);
                    let descuentoDividido = descuento / 100;
                    let totalneto = this.total * descuentoDividido;
                    this.total = this.total - totalneto;

                    this.descuentoPorcentaje = totalneto; 
                    this.descuentoValor = null; 

                    Swal.fire({
                        title: 'Descuento Aplicado',
                        text: `Se le ha aplicado un descuento del ${descuento}%. El precio final es: ${this.formatCurrency(this.total)}`,
                        icon: 'info',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Vaciar los campos de entrada
                        this.descuento = '';
                        this.descuentoper = '';
                    });
                } else if (this.descuentoper) {
                    let descuento = parseFloat(this.descuentoper);
                    this.total = this.total - descuento;

                    // Almacenar el descuento aplicado
                    this.descuentoValor = descuento; // Almacenar el valor del descuento
                    this.descuentoPorcentaje = null; // Si es un descuento fijo, porcentaje es nulo

                    Swal.fire({
                        title: 'Descuento Aplicado',
                        text: `Se le ha aplicado un descuento de ${this.formatCurrency(descuento)}. El precio final es: ${this.formatCurrency(this.total)}`,
                        icon: 'info',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Vaciar los campos de entrada
                        this.descuento = '';
                        this.descuentoper = '';
                    });
                }
            }
        });
    },
    SacarOrdenServicio(){
     axios.get('/SacarOrdenServicio', {
    })
    .then(response => { 
        this.orden = response.data;

    })
    .catch(error => {
        throw error;
    });
    },
    formatDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
         
                return 'Invalid date';
            }

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
    },
    GuardarProductos() {
    // Validar que los campos no estén vacíos
    if (!this.nombrep || !this.referenciap || !this.cantidadp) {
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos',
            text: 'Por favor, complete todos los campos obligatorios: Nombre, Referencia, Cantidad y Precio.',
        });
        return; // Detener la ejecución si hay campos vacíos
    }

      // Verifica si ambos campos están vacíos
        if (!this.precioTotal && !this.precioUnitario) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos vacíos',
                text: 'Debes ingresar el precio total o el precio unitario, al menos uno debe estar lleno.',
            });
            return; // Detener la ejecución si ambos campos están vacíos
        }

        const total = parseInt(this.precioTotal.replace(/[^\d]/g, ''), 10);
        const totalUnidad = parseInt(this.precioUnitario.replace(/[^\d]/g, ''), 10);
        if (this.esPesa === 'Sí') {
            // Valida si el precio total es un número mayor a 0
            if (isNaN(total)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Valor inválido',
                    text: 'El precio total debe ser un valor numérico mayor a 0.',
                });
                return; // Detener la ejecución si hay errores de validación
            }
        } else {
            // Si "No" (el producto no se pesa), valida el precio unitario
            if (isNaN(totalUnidad) ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Valor inválido',
                    text: 'El precio unitario debe ser un valor numérico mayor a 0.',
                });
                return; // Detener la ejecución si hay errores de validación
            }
        }
        // Input d epreioc nuevos 
        const totalC = parseInt(this.precioTotalC.replace(/[^\d]/g, ''), 10);
        const totalUnidadC = parseInt(this.precioUnitarioC.replace(/[^\d]/g, ''), 10);
        if (this.esPesa === 'Sí') {
            // Valida si el precio total es un número mayor a 0
            if (isNaN(total)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Valor inválido',
                    text: 'El precio total debe ser un valor numérico mayor a 0.',
                });
                return; // Detener la ejecución si hay errores de validación
            }
        } else {
            // Si "No" (el producto no se pesa), valida el precio unitario
            if (isNaN(totalUnidad) ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Valor inválido',
                    text: 'El precio unitario debe ser un valor numérico mayor a 0.',
                });
                return; // Detener la ejecución si hay errores de validación
            }
        }


    // Realizar la solicitud si todo está correcto
    axios.get('/GuardarProductos', {
        params: {
            nombrep: this.nombrep,
            referenciap: this.referenciap,
            cantidadp: this.cantidadp,
            preciototal: total,
            preciounidad: totalUnidad,
            categoriap: this.categoriap,
            unidadPeso: this.unidadPeso,
            esPesa: this.esPesa,
            precioTotalC: totalC,
            precioUnitarioC: totalUnidadC,
        }
    })
    .then((response) => {
        Swal.fire({
            icon: 'success',
            title: 'Producto guardado',
            text: 'El producto se ha guardado correctamente.',
        });
        this.nombrep = "";
        this.referenciap = "";
        this.cantidadp = "";
        this.categoriap = "";
        this.unidadPeso = "";
        this.esPesa = "";
        this.preciototal = "";
        this.preciounidad = "";
    })
    .catch((error) => {
        Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: 'Hubo un problema al guardar el producto. Inténtelo de nuevo más tarde.',
        });
    });
    },
    CategoriasListar() {
    axios.get('/CategoriasSupermercado')
    .then(response => {
        this.Categorias = response.data;
        console.log("Datos de Categorías:", this.Categorias); // Imprime los datos en consola
    })
    .catch(error => {
        console.error("Hubo un error al obtener las categorías:", error); // Muestra el error en consola
    });
    },
    },
    mounted() {
        this.BuscarReferenciaProducto();
        this.SacarTotal();
        this.calcularTotal();
        this.SacarCambio();
        this.MostraUsuarios();
        this.RalizarFactura();
        this.RealizaFacturaCreacionClientes();
        this.SacarOrdenServicio();
        this.CategoriasListar();
        this.calcularPrecioTotal();
        this.AgregarProducto();
    }
};
</script>
<style >

/* Estilo para dispositivos móviles */
@media (max-width: 768px) {
    body {
        font-size: 14px;
    }

    .container {
        padding: 10px;
    }
}

/* Estilo para tabletas */
@media (max-width: 1024px) {
    .container {
        padding: 20px;
    }
}

/* Estilo para pantallas grandes */
@media (min-width: 1025px) {
    .container {
        padding: 40px;
    }
}

@media print {
    body {
        width: 58mm;
        margin: 0;
        padding: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px; 
    }

    th, td {
        border: 1px solid black;
        padding: 4px;
        text-align: left;
    }
    .total-row {
        display: table-row; 
        font-weight: bold; 
        text-align: right; 
        padding: 10px; 
        background-color: #f8f9fa; 
        border-top: 2px solid #000; 
        font-size: 16px; 
        border: 2px solid red; 
        width: 100%; 
        box-sizing: border-box; 
    }
    .total-row td {
        border: none; 
    }

}

*{
    margin: 0px;
    padding: 0px;
}
#cabeza{
     height: auto;
     width: 100%;
     display: flex;
}
#Cuerpo{
     width: 72%;
     height: 80%;
     border: 1px solid rgb(179, 179, 179);
     border-radius: 40px;
     margin-right: 30px;
     background: white;
     padding: 10px;
}
#cuerpo2{
    width: 30%;
    height: 90%;

}
#CuerpoClinetes{
    height: 35%;
    border-radius: 30px;
    border: 1px solid rgb(179, 179, 179);
    background: white;
}
#Calcular{
    height: 40%;
    margin-top: 70px;
    border: 1px solid rgb(179, 179, 179);
    border-radius: 30px;
    background: white;
}
#pieDePagina{
    border-top: 2px solid #dee2e6;
    width: 100%;
    height: 170px;
    margin-top: 620px;
    padding:10px 20px;
    display: flex;
}
#text1{
      width: 350px;
    font-size: 18px;
    color: #343a40;
    padding:10px 20px;
     margin: 0;
    font-family: 'Roboto', sans-serif;

}
#Telefonos{
    font-family: 'Roboto', sans-serif;
    padding-top: 15px;
    width: 350px;
}
#Telefonos i {
    margin-right: 5px;
    font-size: 20px;
}
.fa-envelope {
    font-size: 18px;
    margin-right: 10px;
}
#FormularioFooter{
    width: 700px;
    margin-left: 190px;

}
#botonDeProblema{
    border: none;
     margin-left: 80px;
    background-color:white;
    padding: 10px; 
    border-radius: 5px; 
    width: 70%;
}
#botonFormulario{
    width: 90px;
    margin-left: 500px;
    margin-top: -50px;
}
.Busqueda {
    width: 70%;
    height: 50px;
    margin-top: 20px;
    margin-left: 20px;
    display: flex;
    gap: 20px;
}
#search-icon i {
    font-size: 20px; 
    border-radius: 20px;
    
}
.input-group-text {
    padding: 10px 15px; 
}
.form-control {
    font-size: 18px; 
    height: 50px;
    padding: 15px 10px; 
    border-radius: 20px;
}
.ActivarCodigo{
     display: flex;
     width: 240px;
     margin-right: -300px;
     padding: 10px;
}
#CuerpoClientes {
    border: 1px solid #ddd; 
    padding: 15px;
    background-color: #f9f9f9; 
}

#CuerpoClientes h5 {
    margin-bottom: 10px;
}

#CuerpoClientes hr {
    border: 1px solid #000; 
    margin-top: 10px; 
    margin-bottom: 10px; 
}
#BotonBusqueda{
    display: flex;
    width: 500px;
    height: 42px;
    margin-left: 20px;
    margin-top: 15px;
    
}
.titulo{
    margin-left: 17px;
    margin-top: 13px;
    height: 40px;
    width: 310px;
    font-weight: bold;
    display: flex;
    font-size: 18px;
}
.titulo i {
    margin-left: 5px;
}
.hr{
    margin-top:-15px ;
}
.btn-enviar {
    background-color: #1f2124; 
    color: white;
    border: none; 
    border-radius: 5px; 
    padding: 10px 20px; 
    font-size: 15px; 
    font-weight: bold; 
    cursor: pointer;
    display: flex;
    align-items: center; 
    gap: 10px; 
}
.btn-enviar:hover {
    background-color: #1f2124; 
}
.btn-enviar i {
    font-size: 20px; 
}
  table {
        width: 100%;
        border-collapse: collapse;
    }
    th td {
        text-align: center; 
        background-color: #f4f4f4; 
        padding: 10px; 
    }
     .table-responsive {
        border: 1px solid rgb(179, 179, 179);
        height: 400px; 
        overflow-y: auto; 
        overflow-x: auto; 
        border-radius: 10px;
    }
#piedepagina{
    display: flex;
    margin-top: -18px;
    height: 100px;
}
#BotonBusquedaEliminar{
    color: white;
    width: 900px; 
    border-radius: 5px; 
    font-size: 22px; 
    font-weight: bold; 
    cursor: pointer;
    display: flex;
    align-items: center; 
    gap: 10px; 
}
#TotalF {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    border-radius: 5px;
    width: 200px;
    height: 74px;
    padding-right: 30px;
}
#TotalF p {
    margin: 0;
    font-size: 22px; 
    font-weight: bold;
}
.amount {
    font-weight: bold;
    font-size: 18px; 
}
#ClientesC {
    margin-left: 10px;
    width: auto;
    margin-right: 10px;  
    display: flex;
    flex-direction: column;
    align-items: center; 
    justify-content: center; 
}

.input-icon-container {
    display: flex;
    align-items: center;
}
.input-icon-container i {
    color: #2e7d32 ;
    font-size: 26px;
}
#inpudClientes{
    width: 270px;
    height: 38px;
    border-radius: 20px;
    
}
.input-icon-container input {
    margin-right: 10px;
    background-color: #d3d3d3; 
    border: 1px solid white; 
    padding: 0.5rem; 
    font-family: Arial, sans-serif; 
    padding-left: 10px;
}
.Clientes2424{
    padding-left: 10px;
}
#containesDeClientes{
    height: 90px;
    margin-right: 10px;
}
#containesDeClientes {
    display: flex; 
    gap: 20px; 
}
.client-info {
    display: flex;
    flex-direction: column; 
    align-items: flex-start; 
}
.Clientes2424 {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
}
.Clientes242411111 {
    font-weight: bold;
    font-size: 1.2rem;
    margin-bottom: 8px; 
    width: 280px;
}
#inpudClientes1{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    font-weight: bold;
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 100px;
    margin-left: 10px;
    border-radius: 10px;
    text-align: center;
}
 #inpudClientes2{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 150px;
    margin-left: 10px;
    border-radius: 10px;
    font-weight: bold;
    text-align: center;
 }.btn-icon {
    background: none; 
    border: none; 
    color: #2e7d32;
    font-size: 1.5rem; 
    cursor: pointer; 
    padding: 0; 
    display: flex; 
    align-items: center; 
    justify-content: center;
}
.btn-icon:hover {
    color: #2e7d32;
}
#exampleModal .modal-dialog {
    max-width: 750px; 
    margin: auto; 
    margin-top: 30px;
}
#exampleModal .modal-content {
    width: 100%;
}
#InpuDeBusqueda{
    width: 400px;
    margin-left: 50px;
}
.Clientes24241{
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
    width: 100px;
    text-align: center;
    margin-left: 10px;
}
#InpudDeVentas{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    font-weight: bold;
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 100px;
    margin-left: 10px;
    border-radius: 10px;
    text-align: center;
}
#InpudDeVentas1{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 140px;
    border-radius: 10px;
    font-weight: bold;
    text-align: center; 
}
.Clientes2424122{
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
    width: 140px;
    text-align: center;
}
#TotalDeVentas{
    text-align: center;
    padding: 5px;
    margin-bottom: -7px;
    padding-left: 20px;
}
.client-info11{
    margin-left: 20px;
}
form {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  margin: 0 auto;

}
form div {
  margin-bottom: 15px;
}
label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #333;
}
input[type="text"],
input[type="tel"],
input[type="email"],
input[type="date"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 16px;
  color: #333;
  box-sizing: border-box;
  transition: border-color 0.3s;
}
input[type="text"]:focus,
input[type="tel"]:focus,
input[type="email"]:focus,
input[type="date"]:focus {
  outline: none;
}
.btn-success {
  display: inline-block;
  width: 100%;
  padding: 12px 20px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  background-color:#2e7d32; 
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.btn-success:hover {
 background-color: #1f2124; 
}
.btn-invoice {
  background-color: #1f2124; 
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}
.btn-invoice:hover {
  background-color: #1f2124; 
}
.btn-invoice i {
  font-size: 16px;
}
table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  text-align: center;
  vertical-align: middle;
  padding: 8px;
}
th {
  background-color:#1f2124; 
  color: #fff;
}
.modal-container {
  max-height: 70vh; 
  overflow-y: auto; 
  text-align: center;
  width: 1000px;
}
.btn-margin {
  margin: 0 1rem; 
}
.button-container {
    display: flex;
    justify-content: center;  /* Centra los botones horizontalmente */
    align-items: center;      /* Centra los botones verticalmente */
    padding: 5px;
    gap: 20px;                /* Espacio entre los botones */
}

.btn-blue {
    color: white;
    border: none;
    padding-left: 8px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    width: 100px;
    height: 40px;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 8px; 
    text-align: center;
    margin-left: 19.3px;
}
.btn-green {
    color: white;
    border: none;
    border-radius: 8px;
    padding-left: 8px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    text-align: center;
    width: 146px;
    height: 40px;
    margin-bottom: 8px; 
    font-weight: bold;
    margin-right: 19.3px;
}
.btn-blue {
    background-color:#1f2124;
}
.btn-green {
    background-color: #007BFF;
}
.btn-blue i, .btn-green i {
    margin-right: 4px;
}
#Bontondebusqueda{
    width: 50px;
    border-top-right-radius: 10px ;
    border-bottom-right-radius: 10px;
    border: none;
    color: #2e7d32 ;
}
.input-group-text1 {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 24px;

}
.custom-alert {
  border: 1px solid #f39c12; 
  background-color: #fef9e7; 
  color: #e67e22; 
  padding: 15px; 
  border-radius: 5px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
  margin-top: 10px; 
  max-width: 50%; 
  text-align: center;
  margin-left: 25%;
}
.total-row {
    font-weight: bold;            
    text-align: right;           
    padding: 10px;               
    background-color: white;    
    border-top: 2px solid #000;   
    font-size: 16px;              
}
.total-cell {
    border: 2px solid red;        
    width: 100%;                  
    box-sizing: border-box;       
    text-align: right;            
}
.tabla111{
    border: none;
}
.modal-dialog.modal-sm {
    max-width: 600px; 
    display: flex;
    padding: 20px;
}
    .modal-body i {
        font-size: 20px; 
    }
    .label-text {
        font-size: 14px; 
    }
    .input-small {
        max-width: 100px; 
    }
    .modal-body .d-flex {
        align-items: center; 
    }
    .modal-body{
        display: flex;
    }
    #c1{
        width: 50%;
    }
    #c2{
        width: 50%;
    }
    #input1 {
        width: 210px;
        border-radius: 10px;      
    }
    #input2 {
        width: 210px;
        border-radius: 10px;      

    }
    .input-container {
        margin-bottom: 1rem; 
    }
    .d-flex {
        display: flex;
        align-items: center; 
    }
    #i{
        width: 320px;
    }
    #ii{
        width: 320px;
    }
    .input-group {
        display: flex;
        align-items: center; 
        gap: 0.5rem; 
    }
    .input-group i {
        font-size: 28px; 
    }
    #re{
        margin-right: -5.9px;
    }
    #conten{
        width: 800px;
        margin: 0 auto;
        margin-top: 0px;
    }
    #calcular1{
        margin-left: 25px;
    }

    #boton22{
        margin-left: 20px;
        background: #2e7d32;
        color: white;
    }
    

        /* Eliminar bordes en toda la tabla */
    #tablaUsuarios {
        border: none;
        width: 100%;
        margin: 0 auto;
    }
    #tablaUsuarios thead {

        text-align: center; /* Centrado del texto */
    }
    #tablaUsuarios td, #tablaUsuarios th {
        padding: 12px 15px; 
        vertical-align: middle; 
    }
    .btn-invoice {
        background-color: #007bff; 
        border: none;
        color: white;
        padding: 8px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;

        
    }
    .btn-invoice:hover {
        background-color: #0056b3; 
    }
    #tablaUsuarios td, #tablaUsuarios th {
        border: none;
        text-align: center;
    }
    #tablaUsuarios tbody tr {
        border-bottom: 1px solid #ddd; 
    }
    .btn-invoice i {
        margin-right: 5px;
    }
    #ModalCliente {
    width: 100px; 
    height: auto; 
    padding: 10px; 
    margin: auto; 
    box-sizing: border-box; 
    }
    #x{
        font-size: 20px;
        
    }
    #modaldeproducto{
        width: 500px;
    }
    .custom-select {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  appearance: none; /* Elimina estilos predeterminados */
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Flecha personalizada en el select */
.custom-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%233c4043' d='M2 0L0 2h4z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 0.65rem;
}

/* Efecto al enfocar */
.custom-select:focus {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Opciones dentro del select */
.custom-select option {
  font-weight: 400;
  color: #495057;
}

#botonproducto{
  background: #2e7d32;
  color: white;
}
#idfa{
  background: #2e7d32;
  color: white; 
  margin-bottom: 10px;
}
#icnocliente{
    color: #2e7d32 ;
}
#opclientes{
    background: #2e7d32;
    color: white; 
}
#BotonDeCantidad {
    background-color: white; 
    color: black; 
    font-size: 17px; 
    padding: 8px 12px;
    border: 2px solid #ccc; 
    border-radius: 8px; 
    outline: none; 
    text-align: center;
    transition: border-color 0.3s; 
    width: 90px;
}
#BotonDeCantidad:focus {
    border-color: #28a745; 
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.4); 
}
#abono{
        width: 200px;
        height: 40px;
    }
    #BotonDePrecio {
    background-color: white; 
    color: black; 
    font-size: 17px; 
    padding: 8px 12px;
    border: 2px solid #ccc; 
    border-radius: 8px; 
    outline: none; 
    text-align: center;
    transition: border-color 0.3s; 
    width: 110px;
    }
    #BotonDePrecio:focus {
        border-color: #28a745; 
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.4); 
    }
    #FlexProducto {
    display: flex;
    gap: 1rem; 
    align-items: center; 
    justify-content: space-between; 
    }
    #FlexProducto .form-group {
        flex: 1;
        display: flex;
        flex-direction: column; 
    }
    #unitOfWeight {
    width: 100%; 
    padding: 10px; 
    font-size: 16px; 
    border: 1px solid #ccc; 
    border-radius: 4px; 
    background-color: white; 
    color: #333; 
    appearance: none; 
    outline: none; 
    transition: border-color 0.3s, box-shadow 0.3s;
    }
    #unitOfWeight:focus {
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
    }
    #unitOfWeight:hover {
        background-color: #f1f1f1; 
        cursor: pointer;
    }

    #sepesa div {
        display: flex;
        gap: 5px;
        width: 50px;
        margin-left: 10px;
    }
    #productWeightYes{
        transform: scale(1.4);
    }
    #productWeightNo{
        transform: scale(1.4);
    }
    #sipesa{
        padding-top: 10px;
    }
    #nopesa{
        padding-top: 10px;
    }
    #preguntadepeso{
        height: 60px;
    }






</style>