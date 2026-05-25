<template>
  <br>
  <br>
  <div id="ContenedorNav">
  <div class="card">
    <nav class="navbar navbar-expand-lg" id="grandewnav">
      <a class="navbar-brand" href="/Inventario" style="color: black; text-decoration: none;">Inventario</a>

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
              <a class="navbar-brand" href="/Ventas" style="color: black; text-decoration: none;">
                  <i class="bi bi-graph-up-arrow"></i> Ganancias
              </a>

            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</div>
<div>
  <div>

      <div class="card shadow-sm">
        <div class="card-body">
          <div class="card-header">
            <h3 class="mb-0">Listado Completo de Productos del Supermercado</h3>
          </div>
          <div v-if="message" :class="messageClass" class="alert" role="alert">
            {{ message }}
          </div>
          <div class="table-responsive" id="table">
            <table id="tableinv" class="table table-sm">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Referencia</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>UP</th>
                  <th>Categoría</th>
                  <th>Fecha de Vencimiento</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para actualizar producto -->
    <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="updateProductModalLabel1">Actualizar Producto</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="Actualizar">
              <div class="form-group">
                <label for="productReference">Nombre</label>
                <input type="text" v-model="currentProduct.nombre" class="form-control" id="productReference" required autocomplete="off">
              </div>
              <div class="form-group">
                <label for="productReference">Referencia</label>
                <input type="text" v-model="currentProduct.referencia" class="form-control" id="productReference" required autocomplete="off">
              </div>
              <div class="form-group" v-if="currentProduct.estado == 0">
                <label for="productPrice">Precio de venta(Unidad)</label>
                <input type="number" v-model="currentProduct.precio" class="form-control" id="productPrice" required autocomplete="off">
              </div>
            
              <div class="form-group"  v-if="currentProduct.estado == 1">
                <label for="productPrice">Precio de (1Kg)</label>
                <input type="number" v-model="currentProduct.precio" class="form-control" id="productPrice" required autocomplete="off">
              </div>
              <div class="form-group d-flex justify-content-between">
              </div>
              <div class="form-group">
                <label for="productExpirationDate">Fecha de Vencimiento</label>
                <input type="date" v-model="currentProduct.fecha_de_vencimiento" class="form-control" id="productExpirationDate" autocomplete="off" >
              </div>
  


              <button type="submit" class="btn btn-dark">Actualizar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>



<script>
  import axios from 'axios';
  import { formatDistanceToNow } from 'date-fns';
  import { es } from 'date-fns/locale';
  import Swal from 'sweetalert2';
  import * as XLSX from 'xlsx';
  import QRCode from 'qrcode';
  
  export default {
    data() {
      return {
        inventario: [],
        message: '',
        messageClass: '',
        currentProduct: { id_categoria: ""},
        CategoriaP: []
      };
    },
    mounted() {
      this.listarinv();
      this.Categoria();
    },
    methods: {
      
      listarinv() {
      axios.get('/listarinv')
        .then(response => {
          this.inventario = response.data;
          this.$nextTick(() => {
            // Inicializar o destruir DataTable
            if ($.fn.DataTable.isDataTable('#tableinv')) {
              $('#tableinv').DataTable().clear().destroy();
            }
            $('#tableinv').DataTable({
              data: this.inventario,
              columns: [
                { data: 'nombre' },
                { data: 'referencia' },
                { data: 'precio', render: data => this.formatCurrency(data) },
                { data: 'cantidad' },
                {
                  data: 'estado', // Suponiendo que el campo del estado se llama 'estado'
                  render: (data) => {
                      return data === 1 ? 'Kilogramos' : 'Unidad';
                  }
                },
                { data: 'nombre_categoria' },
             
                { data: 'fecha_de_vencimiento', render: data => this.formatFecha(data) },
              
                {
                  data: null,
                  defaultContent: `
                    <div class="btn-container">
                      <button class="btn btn-icon btn-update">
                        <i class="fas fa-pencil-alt"></i>
                      </button>
                      <button class="btn btn-icon btn-delete">
                        <i class="fas fa-trash"></i>
                      </button>
                     
                    </div>
                  `,
                  orderable: false
                }
              ],
              language: {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                sSearch: "Buscar:",
                sLoadingRecords: "Cargando...",
                oPaginate: {
                  sFirst: "Primero",
                  sLast: "Último",
                  sNext: "Siguiente",
                  sPrevious: "Anterior"
                },
                oAria: {
                  sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                  sSortDescending: ": Activar para ordenar la columna de manera descendente"
                }
              },
              drawCallback: () => {
                this.bindButtonEvents();
              }
            });
          });
        })
        .catch(error => {
          this.message = 'Error al obtener los datos';
          this.messageClass = 'alert-danger';
        });
    },
    formatCurrency(amount) {
      const formatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
      });
      return formatter.format(amount);
    },
    formatFecha(fecha) {
      return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    bindButtonEvents() {
      $('#tableinv').off('click', '.btn-update').on('click', '.btn-update', (e) => {
        const row = $(e.currentTarget).closest('tr');
        const data = $('#tableinv').DataTable().row(row).data();
        this.currentProduct = { ...data };
        $('#updateProductModal').modal('show');
      });

      $('#tableinv').off('click', '.btn-delete').on('click', '.btn-delete', (e) => {
        const row = $(e.currentTarget).closest('tr');
        const data = $('#tableinv').DataTable().row(row).data();
        this.Eliminar(data.id_productos);
      });

      $('#tableinv').off('click', '.btn-qr').on('click', '.btn-qr', (e) => {
        const row = $(e.currentTarget).closest('tr');
        const data = $('#tableinv').DataTable().row(row).data();
        this.generarQRCode(data.codigo);
      });
    },

      Eliminar(id_productos) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "No podrás revertir esto",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminarlo',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/Eliminar/${id_productos}`)
              .then(response => {
                Swal.fire({
                  icon: 'success',
                  title: 'Eliminado',
                  text: 'El producto ha sido eliminado correctamente'
                });
                this.listarinv();
              })
              .catch(error => {

                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'No se pudo eliminar el producto'
                });
              });
          }
        });
      },
      formatFecha(fecha) {
          if (!fecha) {
            return 'Fecha no disponible';
          }

          const parsedDate = new Date(fecha);
          if (isNaN(parsedDate.getTime())) {
            return 'Fecha no válida';
          }

          const now = new Date();
          if (parsedDate > now) {
            return `Vencerá en ${formatDistanceToNow(parsedDate, { locale: es, addSuffix: false })}`;
          } else {
            return `Venció hace ${formatDistanceToNow(parsedDate, { locale: es, addSuffix: false })}`;
          }
        },

      triggerFileInput() {
        document.getElementById('fileUpload').click();
      },
      handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('file', file);
         
          axios.post('/exel', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          .then(response => {
            Swal.fire({
            title: "¡Importación Exitosa!",
            text: "El archivo se importó correctamente.",
            icon: "success",
            confirmButtonText: "OK", 
            backdrop: true 
           });
            this.listarinv(); 
          })
          .catch(error => {

            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al importar el archivo',
              confirmButtonText: 'Aceptar'
            });
          });
        }
      },
      openUpdateModal(product) {
        this.currentProduct = { ...product };
        $('#updateProductModal').modal('show');
      },

      Actualizar() {
        axios.put(`/Actualizar/${this.currentProduct.id_productos}`, this.currentProduct)
          .then(response => {
            Swal.fire({
                title: "¡Actualización Exitosa!",
                text: "El producto se actualizó correctamente.",
                icon: "success",
                confirmButtonText: "OK"
            });
            $('#updateProductModal').modal('hide');
            this.listarinv(); 
          })
          .catch(error => {
       
            if (error.response && error.response.data.errors) {
              this.message = Object.values(error.response.data.errors).flat().join('<br>');
              this.messageClass = 'alert-danger';
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el producto',
                confirmButtonText: 'Aceptar'
              });
            }
          });
      },
      cerrarModal() {
    $('#qrModal').modal('hide');
  },

    Categoria(){
      axios.get('/CategoriaRegister', {
      })
      .then(function (response) {
      this.CategoriaP = response.data;
      })
      .catch(function (error) {
      
      });
    },
      generarQRCode(codigo) {
      const codigoString = String(codigo);

      const canvas = document.getElementById('qrCanvas');
      if (canvas) {
        QRCode.toCanvas(canvas, codigoString, { width: 300 }, function (error) {
          if (error) {
            console.error('Error al generar el código QR:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al generar el código QR'
            });
          } else {
            $('#qrModal').modal('show');
          }
        });
      } else {
        console.error('No se encontró el elemento canvas para generar el código QR.');
      }
    }
    }
  };
</script>

<style>

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



/* Aseguramos que el contenedor principal ocupe toda la pantalla */
#todo {
    height: 100vh; /* Altura completa de la ventana */
    width: 100%;
    background-color: #f8f9fa; 
    display: flex;
    flex-direction: column;
}

/* Estilo para el contenedor que envuelve todo el contenido */
.container {
    padding: 8px;
    max-width: 100%; /* Asegura que el contenedor ocupe todo el espacio */
}

/* Diseño de la tarjeta que contiene la tabla */
.card {
    border: none;
    border-radius: 10px;
    background-color: #ffffff; 
    margin: 20px 0; /* Añade márgenes alrededor de la tarjeta */
    width: 100%; /* La tarjeta ocupa el 100% del contenedor */
}

/* Estilo de la cabecera de la tarjeta */
.card-header {
   color: #020202;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    background-color: #ffffff; /* Fondo verde */
}

/* Estilo de la tabla */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    padding-top: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border-bottom: 1px solid #dee2e6; 
}

.table th {
    background-color: #28a745;
    color: white;
    font-weight: bold;
}

.table tbody tr:hover {
    background-color: #f1f1f1; 
}

/* Modal de actualización */
.modal-content {
    border-radius: 8px;
    border: none;
}

.modal-header {
    background-color: #28a745;
    color: white;
}

.modal-body {
    padding: 20px;
}

.form-group label {
    font-weight: bold;
    color: #212529;
}

/* Input y textarea con borde verde en el enfoque */
input.form-control:focus, textarea.form-control:focus {
    border-color: #28a745; 
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); 
}

/* Botones con fondo verde y borde redondeado */
button.btn-dark {
    background-color: #28a745; 
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
}

button.btn-dark:hover {
    background-color: #218838; 
}

/* Estilo del modal QR */
#qrModal .modal-header {
    background-color: #212529; 
    color: white;
}

#qrModal .modal-body {
    text-align: center;
}

#qrCanvas {
    border: 1px solid #28a745; 
}
#ContenedorNav{
  margin-top: -40px;
}
</style>