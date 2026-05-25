<template>
  <br>
  <div id="ContenedorNav">
  <div class="card">
    <nav class="navbar navbar-expand-lg" id="grandewnav">
        <a class="navbar-brand" href="/Inventario">Inventario</a>
        <!-- Botón para togglear el menú en pantallas pequeñas -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú alineado a la derecha -->
        <div class="container d-flex justify-content-between align-items-center">
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item mx-3">
              <a class="nav-link" href="/RegistrarProductos">
                <i class="fas fa-plus-circle me-2"></i> Registrar Producto
              </a>
            </li>
            <li class="nav-item mx-3">
              <a class="nav-link" href="/Agregar">
                <i class="fas fa-box-open me-2"></i> Agregar Productos-Almacén
              </a>
            </li>
            <li class="nav-item mx-3">
              <a class="nav-link" href="/Ventas">
                <i class="fas fa-box-open me-2"></i> Ganancias
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</div>





    <div class="container">
      <div class="card search-card">
        <div class="card-body">
          <div class="search-container">
            <input
              type="text"
              placeholder="Buscar en la tabla..."
              class="search-input"
              v-model="buscar"
              @input="buscarReferencia"
            />
            <div class="cantidad-productos">
              <div class="cantidad-icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-database" viewBox="0 0 16 16">
                    <path d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698M14 4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16s3.022-.289 4.096-.777C13.125 14.755 14 14.007 14 13zm-1 4.698V10c0 .374-.356.875-1.318 1.313C10.766 11.729 9.464 12 8 12s-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10s3.022-.289 4.096-.777A5 5 0 0 0 13 8.698m0 3V13c0 .374-.356.875-1.318 1.313C10.766 14.729 9.464 15 8 15s-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13s3.022-.289 4.096-.777c.324-.147.633-.323.904-.525"/>
                  </svg>
              </div>
              <span class="cantidad-text">
                Cantidad: {{ cantidadProducto }}
              </span>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal -->
      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" v-for="hola in refe" :key="hola.id_productos">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Agregar Cantidad de Productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="cantidadProducto" class="form-label">Cantidad</label>
                    <input
                        type="number"
                        class="form-control"
                        id="cantidadProducto"
                        placeholder="Ingresa la cantidad"
                        v-model="cantidadAgregar"
                         autocomplete="off"
                    />
                </div>

                <!-- Agregado de unidad de peso -->
                <div class="form-group" v-if="this.estado == 1">
                    <label for="unitOfWeight" class="form-label">Escoge unidad de peso</label>
                    <select
                        id="unitOfWeight"
                        v-model="unidadPeso"
                        @change="ActualizarUnidadPeso(index)"
                    >
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="kilogramos">Kilogramos (kg)</option>
                        <option value="gramos">Gramos (g)</option>
                        <option value="miligramos">Miligramos (mg)</option>
                        <option value="libras">Libras (lb)</option>
                        <option value="onzas">Onzas (oz)</option>
                        <option value="toneladas">Toneladas (t)</option>
                    </select>
                </div>

                <!-- Nuevo input para el precio del producto -->
                 <div v-for="hola in refe" :key="hola.id_productos"  >
                  <div class="mb-3" v-if="this.estado == 0">
                      <label for="precioProducto" class="form-label">Precio de compra (Individual)</label>
                      <input
                          type="text"
                          class="form-control"
                          :id="'precioProducto' + hola.id_productos"
                          placeholder="Ingresa el precio"
                          v-model="hola.precioC"  
                          @input="formatearPrecio1"
                            autocomplete="off"
                        
                      />
                  </div>
                </div>

                <div  v-if="this.estado == 0">
                <div v-for="hola in refe" :key="hola.id_productos" >
                  <label for="precioProducto" class="form-label">Precio  de venta (Individual)</label>
                  <input
                    type="text"
                    class="form-control"
                    :id="'precioProducto' + hola.id_productos"
                    placeholder="Ingresa el precio"
                    v-model="hola.precio"  
                    @input="formatearPrecio(hola)" 
                    autocomplete="off"
                  />
                </div>
              </div>

                <!-- Nuevo input para el total del producto -->
                 <div v-for="hola in refe" :key="hola.id_productos">
                    <div class="mb-3" v-if="this.estado == 1">
                        <label for="totalProducto" class="form-label">Precio de compra( 1Kg )</label>
                        <input
                            type="text"
                            class="form-control"
                            id="totalProducto"
                            placeholder="Total"
                            v-model="hola.precioC"
                            @input="formatearPrecio1"
                            autocomplete="off"
                        />
                    </div>
                  </div>
                  <div  v-if="this.estado == 1">
                    <div class="mb-3" v-for="hola in refe" :key="hola.id_productos">
                        <label for="precioProducto" class="form-label">Precio de venta(1 Kg)</label>
                        <input
                            type="text"
                            class="form-control"
                            id="precioProducto"
                            placeholder="Ingresa el precio"
                            v-model="hola.precio"
                            @input="formatearPrecio1"
                            autocomplete="off"
                          
                        />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" @click="agregarCantidad(hola)">Agregar</button>
            </div>
        </div>
    </div>
</div>


  

   
  
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Referencia</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Categoría</th>
              <th>Fecha de Vencimiento</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="buscar in refe" :key="buscar.id_productos">
              <td>{{ buscar.nombre }}</td>
              <td>{{ buscar.referencia }}</td>
              <td>{{ formatCurrency(buscar.precio) }}</td>
              <td>{{ buscar.cantidad }}</td>
              <td>{{ buscar.nombre_categoria }}</td>
              <td>{{ buscar.fecha_vencimiento }}</td>
              <td>
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop" @click="AgregarProductos(refe,buscar.estado,buscar.precio)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                    <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
                    <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
<script>
  import axios from 'axios';
  import Swal from 'sweetalert2'; 
  
  export default {
    data() {
      return {
        buscar: '', 
        refe: [], 
        cantidadProducto: null,
        cantidadAgregar: 1,
        productoActual: null,
        productosRecientes: [],
        unidadPeso: '',
        estado:'',
        precioTotalDC:0,
        totalProducto : 0,
     
      };
    },
    methods: {
      formatearPrecio1() {
        let precioFormateado = String(this.precioTotalDC);  // Aseguramos que es una cadena
        precioFormateado = precioFormateado.replace(/\D/g, ''); // Elimina caracteres no numéricos
        if (precioFormateado.length > 3) {
            precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Agrega puntos de miles
        }
        this.precioTotalDC = precioFormateado ? '$ ' + precioFormateado : ''; // Agrega el signo de pesos
        },
      formatearPrecio() {
        let precioFormateado = String(this.totalProducto);  // Aseguramos que es una cadena
        precioFormateado = precioFormateado.replace(/\D/g, ''); // Elimina caracteres no numéricos
        if (precioFormateado.length > 3) {
            precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Agrega puntos de miles
        }
        this.totalProducto = precioFormateado ? '$ ' + precioFormateado : ''; // Agrega el signo de pesos
        },
      buscarReferencia() {
        axios
          .get('/obtenerproductoreferencia', {
            params: {
              busqueda: this.buscar,  // Usamos un parámetro genérico para la búsqueda
            },
          })
          .then((response) => {
            this.refe = response.data;  // Suponiendo que la respuesta contiene una lista de productos
            this.obtenerCantidadProducto(this.buscar);
          })
          .catch((error) => {
            console.error('Error al realizar la búsqueda:', error);
          });
      },

      obtenerCantidadProducto(referencia) {
        axios
          .get(`/cantidadProducto`, {
            params: { referencia }  // Pasamos la referencia como un parámetro
          })
          .then((response) => {
            this.cantidadProducto = response.data.cantidad;
          })
          .catch((error) => {
            console.error('Error al obtener la cantidad del producto:', error);
          });
      },


      AgregarProductos(refe,buscar,precio) {
        
        if (!refe) {
        console.error("No se recibió ningún producto");
        return;
       }
  
        this.productoActual = refe;
       
        this.estado = buscar;
      
        this.precio = precio;
        
      },
      formatCurrency(amount) {  // Format Colombian currency
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
        },
  
          agregarCantidad(hola) {
        if (this.cantidadAgregar <= 0 ) {
          Swal.fire('Error', 'Ingrese una cantidad válida y seleccione un producto.', 'error');
          return;
        }
        
        axios
          .post('/agregarproductos', {
            producto: this.productoActual,
            cantidad: this.cantidadAgregar,
            unidadPeso:  this.unidadPeso,
            precioID: hola.precio,
            precio: this.totalProducto,
            precioTotalDC: hola.precioC
          })
          .then((response) => {
            if (response.status === 200 ) {
              Swal.fire('Éxito', 'Productos agregados exitosamente.', 'success'); 
              this.cantidadAgregar = 1;
              this.precioTotalDC = '';
              this.totalProducto = '';    
              this.unidadPeso = ''; 
              location.reload();
              this.obtenerCantidadProducto(this.productoActual.referencia);
              this.productosRecientes = response.data.productos;
  
              $('#addedProductsModal').modal('show');
            } else {
              Swal.fire('Error', 'No se pudieron agregar los productos. Intente nuevamente.', 'error');
            }
          })
          .catch((error) => {
            Swal.fire('Error', 'No se pudo agregar los productos.', 'error');
          });
      },
      handleCloseAndRefresh() {
        $('#addedProductsModal').modal('hide');
        window.location.reload();
    },
      actualizarTalla(producto) {
        axios
          .post(`/actualizar-talla/${producto.codigo}`, {
            talla: producto.talla,
          })
          .then((response) => {
          })
          .catch((error) => {
            console.error('Error al actualizar la talla:', error);
            Swal.fire('Error', 'No se pudo actualizar la talla.', 'error');
          });
      },

    guardarEdiciones() {
      const promises = this.productosRecientes.map(producto => {
        return this.actualizarTalla(producto);
      });
      Promise.all(promises)
        .then(() => {
          window.location.reload();
        })
        .catch(error => {
          console.error('Error al actualizar las tallas:', error);
          Swal.fire('Error', 'No se pudieron actualizar todas las tallas.', 'error');
        });
    },
  
    },
  };
</script>
  
  <style scoped>
  
  /* Estilos para el modal de productos añadidos */
  .table {
    color: #333;
  }
  .table thead {
    background-color: #ffffff;
    color: #fff;
  }
  .table tbody tr:hover {
    background-color: #dbdbdb;
  }
  .modal-header {
    background-color: white;
    color: #fff;
  }
  .modal-footer {
    background-color: #f9f9f9;
  }
  
  .container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .card {
    border: none;
    border-radius: 10px;
    background-color: #ffffff; 
    width: 100%; /* La tarjeta ocupa el 100% del contenedor */
}
  
  .card-body {
    padding: 20px;
  }
  
  .search-card {
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .search-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .search-input {
    width: 250px;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background-color: #f9f9f9;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  }
  
  .cantidad-productos {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #ffffff; /* Fondo blanco para un look limpio */
    padding: 12px 16px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08); /* Sombras más marcadas */
    border: 1px solid #e0e0e0; /* Borde sutil */
    transition: box-shadow 0.3s ease; /* Transición suave para efectos de hover */
  }
  
  .cantidad-productos:hover {
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15), 0 2px 5px rgba(0, 0, 0, 0.1); /* Efecto de hover más pronunciado */
  }
  
  .cantidad-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px; /* Tamaño del contenedor del icono */
    height: 20px; /* Tamaño del contenedor del icono */
    background-color: #f5f5f5; /* Fondo claro para el contenedor del icono */
    border-radius: 50%; /* Forma circular para el fondo del icono */
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra interna para el icono */
  }
  
  .cantidad-icon {
    font-size: 20px; /* Tamaño del icono */
    color: #333; /* Color del icono */
  }
  
  .cantidad-text {
    color: #333;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
  }
  
  .cantidad-text strong {
    color: #000;
    font-weight: 600;
    margin-left: 5px;
  }
  
  .table-wrapper {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .data-table th,
  .data-table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
    color: #000;
  }
  
  .data-table th {
    background-color: #f5f5f5;
    font-weight: bold;
  }
  
  .data-table tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  
  .data-table tr:hover {
    background-color: #f5f5f5;
  }
  
  .modal-content {
    border-radius: 10px;
    background-color:white;
  }
  
  .modal-header {
    border-bottom: 1px solid #ddd;
  }
  
  .modal-title {
    color: #333;
    font-weight: bold;
  }
  
  .modal-body {
    padding: 10px;
    max-height: 500px; 
    overflow-y: auto; 
    }

  
  .modal-footer {
    border-top: 1px solid #ddd;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    border: none;
  }
  
  .btn-primary {
    background-color: #333;
    border: none;
  }
  #modal{
    margin-top: -200px;
  }
  .table {
          width: 100%; 
          margin: 0 auto;
    }
    #table{
        text-align: center;
        font-size: 28px;
        border-collapse: collapse;
        width: 100%;
        height: 68%;
    }
    .table, .table th, .table td {
    border: none; 
    }
    .table-group-divider td {
    text-align: center; 
    }
    #tableth{
    text-align: center;
    border: none;
    background-color: white;
    }
    #todonav{
        display: flex;
        margin-bottom: 40px;
        margin-top: -10px;
    }
    #card {
      padding: 10px;
      border-radius: 16px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
      padding-left: 15px;
      height: 80px;
      font-size: 16px;
    }
    #ContenedorNav{
      width: 90%;
      height: 60px;
      margin-left: 65px;
      margin-bottom: 30px;

    }
   
    .navbar {
      border-bottom: 1px solid #e0e0e0;
      text-align: center; /* Centra el contenido en el eje vertical */
      background-color: transparent; /* Elimina el fondo blanco y lo hace transparente */

  }

  /* Links styling */
  .navbar .nav-link {
      font-size: 1rem;
      font-weight: 500;
      color: #333; /* Negro suave */
      transition: color 0.3s ease, border-bottom 0.3s ease;
      position: relative;
      padding-bottom: 5px;
  }

  /* Add underline effect on hover */
  .navbar .nav-link::after {
      content: "";
      position: absolute;
      width: 0;
      height: 2px;
      background-color: #2e7d32; /* Verde oscuro */
      bottom: 0;
      left: 0;
      transition: width 0.3s ease;
  }

  .navbar .nav-link:hover::after {
      width: 100%;
  }

  .navbar .nav-link:hover {
      color: #2e7d32;
      text-decoration: none;
  }

  /* Add spacing between nav items */
  .navbar .nav-item {
      margin-left: 15px;
      margin-right: 10px;
  }

  /* Toggler (Mobile) styling */
  .navbar-toggler {
      border-color: transparent;
  }

  .navbar-toggler-icon {
      display: block;
      width: 25px;
      height: 3px;
      background-color: #333; /* Color de las líneas del toggler */
      position: relative;
  }

  .navbar-toggler-icon::before,
  .navbar-toggler-icon::after {
      content: "";
      display: block;
      width: 25px;
      height: 3px;
      background-color: #333; /* Color de las líneas del toggler */
      position: absolute;
      left: 0;
  }

    .card-header {
      background-color: #ffffff;
      color: white;
      font-size: 1.25rem;
      font-weight: bold;
      padding-left: 0px;
    }
    #inventario-button {
      background-color: black; 
      color: white; 
      width: 120px;
      border-radius: 8px;
      transition: background-color 0.3s; 
    }
    #inventario-button:hover {
          background-color: #333; 
    }
    #container{
      display: flex;
      gap: 10px; 
      justify-content: center; 
      align-items: center; 
    }
    #grandewnav{

     padding-left: 20px;
     height: 80px;
     display: flex;
     border-radius: 10px;
    }
    #navbarNav{
      margin-left: 350px;
    }
    .data-table th, .data-table td {
    text-align: center;
}
/* Estilos para el modal */
.modal-content {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border: none;
    padding: 20px;
}

/* Estilo del encabezado del modal */
.modal-header {
    border-bottom: 1px solid #e3e3e3;
    background-color: #f7f7f7;
    padding: 15px;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

/* Título del modal */
.modal-title {
    font-size: 1.25rem;
    color: #333;
    font-weight: 600;
}

/* Estilo de los botones */
.btn-close {
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    color: #888;
    border-radius: 50%;
    font-size: 1.2rem;
}

/* Estilo de los campos dentro del modal */
.modal-body {
    padding: 20px;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 8px;
    font-size: 1rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 12px 15px;
    font-size: 1rem;
    width: 100%;
    margin-bottom: 15px;
    transition: border 0.3s;
}

.form-control:focus {
    border-color: #007bff; /* Azul vibrante al enfocarse */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Azul vibrante al enfocarse */
}

select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 12px 15px;
    font-size: 1rem;
    width: 100%;
    transition: border 0.3s;
}

select:focus {
    border-color: #007bff; /* Azul vibrante al enfocarse */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Azul vibrante al enfocarse */
}

/* Estilo para los botones del modal */
.modal-footer {
    border-top: 1px solid #e3e3e3;
    text-align: right;
    padding: 10px 20px;
}

.btn-secondary {
    background-color: #f1f1f1;
    color: #333;
    border: 1px solid #ddd;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background-color: #e4e4e4;
}

.btn-primary {
    background-color: #007bff; /* Azul vibrante */
    color: #fff;
    border: none;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3; /* Azul más oscuro al hacer hover */
}

  </style>