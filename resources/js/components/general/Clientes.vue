<template>
    <div class="container">
      <div class="card search-card">
        <div class="card-body">
          <div class="search-container">
            <input
              type="text"
              placeholder="Buscar por Nombre"
              class="search-input"
              v-model="buscar"
              @input="buscarcliente"
            />

          </div>
        </div>
      </div>
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Documento</th>
              <th>Historial de Deudas</th>
              <th>Crédito</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cliente in clientes" :key="cliente.id_cliente">
              <td>{{ cliente.nombre }}</td>
              <td>{{ cliente.documento }}</td>
              <td>
                <button class="btn" style="background-color: #28a745; color: white;" data-toggle="modal" data-target="#myModal" @click="listarProductosDeudas(cliente.documento)" >
                  Historial de Deudas
                </button>
              </td>
              <td>
                <span v-if="cliente.total_deuda > 0 ">
                  <i class="fas fa-check" style="color: green;"></i> 
                </span>
                <span v-else>
                  <i class="fas fa-times" style="color: red;"></i>
                </span>
              </td>

              <td>
                <button class="btn btn-icon btn-update" @click="abrirModalActualizar(cliente)">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn btn-icon btn-delete" @click="eliminarCliente(cliente.id_cliente)">
                  <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-icon btn-update" data-toggle="modal" data-target="#exampleModalCenter" @click="DatosClientes(cliente.id_cliente)">
                  <i class="fas fa-dollar-sign"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Modal para actualizar cliente -->
  <div class="modal fade" id="modalActualizar" tabindex="-1" aria-labelledby="modalActualizarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
  <div class="modal-content" id="ContenidoModalCliente">
  <div class="modal-header">
    <h5 class="modal-title" id="modalActualizarLabel">Actualizar Cliente</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body" id="formclientes">
    <form @submit.prevent="Actualizar(clienteSeleccionado.id_cliente)">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" v-model="clienteSeleccionado.nombre" id="nombre" required autocomplete="off" />
      </div>
      <div class="mb-3">
        <label for="documento" class="form-label">Documento</label>
        <input type="text" class="form-control" v-model="clienteSeleccionado.documento" id="documento" required autocomplete="off" />
      </div>
      <button type="submit" class="btn btn" id="ActualizarClientes">Actualizar</button>
    </form>
  </div>
  </div>
  </div>
  </div>
  </div>
  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content" id="CuerpoDePago">
  <div class="modal-header">
  <h5 class="modal-title" id="exampleModalLongTitle">Formulario de Pago</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  </div>
  <div class="modal-body" >
  <p><strong>Nombre del Cliente:</strong>   {{ NombreAbono }} </p>
  <p><strong>Estado de Pago:</strong> Debe una suma de {{(formatCurrency(suma_debe))}} </p>
      <div class="input-group mb-3"  id="inputabonar"  >
        <span class="input-group-text">$</span>
        <input type="text"  v-model="formattedAbono" class="form-control" aria-label="Amount (to the nearest dollar)" @input="formatInput"
        >
    </div>
  </div>
  <div class="modal-footer">
  <button type="button" id="completo"  class="btn btn-secondary" @click="AbonoTotal(documentoAbono)" >PAGO COMPLETO</button>
  <button type="button" class="btn btn" id="BotonDeAbono" @click="abonocliente(abonocliente)" >Abonar</button>
  </div>
  </div>
  </div>
  </div>
  <!-- Modal de productos que debe -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content" id="CuerpoProductos">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Historial de Deudas</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body" id="CuerpoProductosDeudas">
    <table class="table table" style="border: none;">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Referencia</th>
            <th>Cantidad</th>
            <th>UP</th>
            <th>Precio</th>
            <th>Fecha de compra</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="Producto in Productos" :key="Producto.id">
            <td>{{Producto.nombre }}</td>
            <td>{{Producto.referencia  }}</td>
            <td>{{Producto.cantidad  }}</td>
            <td>{{ Producto.UP  }}</td>
            <td>{{formatCurrency(Producto.total)}}</td>
            <td>{{ formatDate(Producto.fecha_de_creacion)  }}</td>
        </tr>
    </tbody>
    </table>
    </div>
    <div class="modal-footer d-flex justify-content-between w-100">
    <div class="left-text">
        <span class="abono">Abono: {{formatCurrency(Abonos) }}</span>
        <span class="debe">Debe: {{formatCurrency(sumaDebe) }}</span>
    </div>

      <div class="right-buttons">
          <button type="button" class="btn btn-danger" id="botonPDF" @click="generatePdf">
              <i class="fas fa-file-pdf"></i> Descargar PDF
          </button>
          <button type="button" class="btn btn" id="botondedeudas" data-dismiss="modal">Cerrar</button>
      </div>
  </div>


    </div>
    </div>
    </div>
    </template>
  
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  import { jsPDF } from 'jspdf';
  import autoTable from 'jspdf-autotable';
  import 'sweetalert2/dist/sweetalert2.min.css';
  
  export default {
    data() {
      return {
        clientes: [],
        cumpleaneros: [],
        buscar: '',
        seleccionados: [],
        seleccionar: false,
        clienteSeleccionado: {},
        correosSeleccionados: [], 
        suma_debe:'',
        AbonoApartado: 0,
        documentoAbono:'',
        inputabonar1:'',
        NombreAbono:'',
        documento:'',
        formattedAbono: '', 
        Productos: [],
        sumaDebe: '',
        Abonos:'',
      };
    },
  
    methods: {
      formatDate(dateString) {
      const date = new Date(dateString);

      const optionsDate = { day: 'numeric', month: 'long' }; 
      const optionsTime = { hour: 'numeric', minute: '2-digit', hour12: true }; 
    
      const formattedDate = date.toLocaleDateString('es-CO', optionsDate); // Formato: 1 de mayo
      const formattedTime = date.toLocaleTimeString('es-CO', optionsTime); // Formato: 1:03 PM
      return `${formattedDate} / ${formattedTime}`;
      },
      listar() {
        axios
          .get('/listar')
          .then((response) => {
            this.clientes = response.data;
          })
          .catch((error) => {

          });
      },
      buscarcliente() {
    axios
      .get('/buscarcliente', {
        params: {
          query: this.buscar
        }
      })
      .then((response) => {
        if (response.data.length === 0) {
          Swal.fire({
            icon: 'error',
            title: 'No encontrado',
            text: 'No se encontraron clientes con esa búsqueda.',
          });
        } else {
          this.clientes = response.data;
        }
      })
      .catch((error) => {

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Ocurrió un error al buscar el cliente.',
        });
      });
      },
      abrirModal() {
        this.actualizarCorreos(); 
        $('#modalEnviarCorreo').modal('show');
      },
      cerrarModal() {
        $('#modalEnviarCorreo').modal('hide');
        this.correo.para = [];
        this.seleccionados = [];
      },
      abrirModalActualizar(cliente) {
        this.clienteSeleccionado = { ...cliente };
        $('#modalActualizar').modal('show'); 
      },
      Actualizar(id_cliente) {
        axios.put(`/actualizarCliente/${id_cliente}`, this.clienteSeleccionado)
              .then((response) => {
                  Swal.fire('Actualizado!', 'Cliente actualizado con éxito!', 'success').then(() => {
                      this.listar(); 
                      $('#modalActualizar').modal('hide');
                      location.reload(); 
                  });
              })
              .catch((error) => {

                  Swal.fire('Error!', 'Error al actualizar el cliente!', 'error');
              });
      },
      eliminarCliente(id_cliente) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "No podrás revertir esto!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminar!'
        }).then((result) => {
          if (result.isConfirmed) {
            axios
              .delete(`/eliminarCliente/${id_cliente}`)
              .then((response) => {
                Swal.fire('Eliminado!', 'Cliente eliminado con éxito!', 'success');
                this.listar(); 
              })
              .catch((error) => {

                Swal.fire('Error!', 'Error al eliminar el cliente!', 'error');
              });
          }
        });
      },
      DatosClientes(id_cliente) {
      axios.get('/datosClientesAbono', {
        params: {
            id_cliente: id_cliente
        }
       })
      .then(response => {
          this.NombreAbono = response.data.nombre;
          this.suma_debe = response.data.suma_debe;
          this.documentoAbono = response.data.documento;

      })
      .catch(error => {
          console.error(error);
      });
      },
      AbonoTotal(documentoAbono) {
      Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Estás seguro de que el cliente va a pagar todo lo que debe?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, estoy seguro',
          cancelButtonText: 'Cancelar'
      }).then((result) => {
          if (result.isConfirmed) {
              axios.get('/AbonoTotal', {
                  params: {
                      documentoAbono: documentoAbono,
                  }
              })
              .then(response => {
                  Swal.fire({
                      icon: 'success',
                      title: 'Éxito',
                      text: 'Abono exitoso para el cliente. ¡Gracias!',
                  });
              })
              .catch(error => {
                  if (error.response && error.response.data.error) {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: error.response.data.error,
                      });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Ocurrió un error al procesar la solicitud.',
                      });
                  }
                  console.error(error);
              });
          } else {
              Swal.fire({
                  icon: 'info',
                  title: 'Cancelado',
                  text: 'La operación ha sido cancelada.',
              });
          }
      });
      },
      abonocliente() {
    // Validar si el campo está vacío
    if (!this.inputabonar1 || this.inputabonar1.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'El campo de abono no puede estar vacío.',
        });
        return; // Detener ejecución si el campo está vacío
    }

    const debeActual = parseFloat(this.suma_debe); 
    const Abono = parseFloat(this.inputabonar1); 
    const debeActualFormatted = this.formatCurrency(debeActual);

    // Verificar si el abono es mayor que la deuda
    if (Abono > debeActual) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `No puedes abonar más de lo que debes (${debeActualFormatted}).`,
        });
        return; // Detener ejecución si el abono es mayor que la deuda
    }

    // Confirmación antes de procesar el abono
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a abonar ${this.formatCurrency(Abono)} para el cliente con el documento ${this.documentoAbono}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar abono'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get('/Abono', {
                params: {
                    documentoAbono: this.documentoAbono,
                    Abono: this.inputabonar1
                }
            })
            .then(response => {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.data.success,
                });
                this.formattedAbono = ''; // Limpiar campo de abono después de la respuesta exitosa
            })
            .catch(error => {
                // Manejar errores
                if (error.response && error.response.data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.error,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.',
                    });
                }
                console.error(error);
            });
        }
    });
      },
      formatInput(event) {
      const value = event.target.value.replace(/[^\d]/g, '');  // Elimina cualquier caracter no numérico
      this.inputabonar1 = value;  // Guardar el valor sin formato en inputabonar1
      this.formattedAbono = this.formatCurrency(value);  // Actualizar el valor formateado para mostrarlo
      },
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
      listarProductosDeudas(documento) {
        const me = this; 
        me.DatosClientes();
        axios.get('/ListarPD',{
          params:{
            documento:  documento
          }
        })
          .then((response) => {
            this.Productos = response.data.itemsArray;
            this.sumaDebe = response.data.sumaDebe;
            this.Abonos = response.data.Abonos;
          })
          .catch((error) => {

          });
      },
      generatePdf() {
        const doc = new jsPDF(); // Crear un nuevo documento PDF
        const headers = ["Nombre", "Referencia", "Cantidad", "UP", "Precio", "Fecha de Compra"];
        const rows = []; 

        doc.setFontSize(16);
        doc.text("Reporte de Productos Entregados al Cliente", 14, 20); 
        doc.setFontSize(12);
        // Alinear el texto de "Abono" y "Debe" en una sola línea.
        doc.text(`Abono: ${this.formatCurrency(this.Abonos)}  |  Debe: ${this.formatCurrency(this.sumaDebe)}`, 14, 30);
  


        const totalEarnings = this.Productos.reduce((acc, item) => acc + item.precio, 0); // Calcula el total de ganancias

        // Rellenar las filas con datos de Productos
        this.Productos.forEach((DatosCliente) => {
            const row = [
                DatosCliente.nombre,
                DatosCliente.referencia,
                DatosCliente.cantidad,
                DatosCliente.UP,
                this.formatCurrency(DatosCliente.total),
                this.formatDate(DatosCliente.fecha_de_creacion)
            ];
            rows.push(row);
        });

        // Crear la tabla
        doc.autoTable({
            head: [headers], // Pasar encabezados
            body: rows, // Pasar filas
            startY: 40, // Espacio inicial en el eje Y
            theme: 'grid', // Tema de la tabla
        });

        // Mostrar o descargar el PDF
        doc.output('dataurlnewwindow'); // Abre el PDF en una nueva ventana
    },

    },
    mounted() {
      this.listar();
      this.DatosClientes();
      this.listarProductosDeudas();
    },
  };
  </script>
  
  
  <style scoped>
  
  .modal-content {
      border-radius: 1rem; /* Bordes redondeados */
  }
  
  .modal-header {
      border-bottom: none; /* Sin borde en el header */
  }
  
  .list-group-item {
      transition: background-color 0.3s;
      border: none; /* Efecto de transición */
  }
  .list-group {
    max-height: 500px; 
    overflow-y: auto;  

  }
  
  .list-group-item:hover {
      background-color: #ffffff; /* Color de fondo al pasar el ratón */
  }
  
  .modal-footer {
      border-top: none; /* Sin borde en el footer */
  }
  
  .btn-primary {
      background-color: #007bff; /* Color del botón */
      border: none; /* Sin borde */
  }
  
  .btn-primary:hover {
      background-color: #0056b3; /* Color del botón al pasar el ratón */
  }
  
  .notification-icon-container {
    position: relative; 
    margin-right: -700px; 

  }
  
  .notification-button {
    background: transparent; 
    border: none; 
    cursor: pointer; 
    font-size: 28px;
    position: relative;
  }
  
  .notification-button:hover {
    color: #0099ff; /* Cambia el color al pasar el ratón */
  }
  
  .notification-count {
    position: absolute;
    top: -5px;
    right: -10px;
    background-color: #2e7d32; /* Color de fondo del contador */
    color: white; /* Color del texto del contador */
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
  }
  
  .btn-gmail {
    background-color: #2e7d32; /* Color de fondo del botón */
    color: white; /* Color del texto del botón */
    border: none; /* Sin borde */
    padding: 10px 15px; /* Espaciado interno */
    border-radius: 5px; /* Bordes redondeados */
    cursor: pointer; /* Cambia el cursor al pasar el ratón */
  }
  
  .btn-gmail:hover {
    background-color: #2e7d32; /* Color más oscuro al pasar el ratón */
  }
  
  
  .custom-input {
    border-radius: 0.25rem; /* Ajusta el radio de borde */
  }
  
  .input-group-text {
    background-color: #f8f9fa; /* Color de fondo para el contenedor del icono */
    border: 1px solid #ced4da; /* Borde del icono */
  }
  .modal-content {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  
  .modal-header {
    background-color: #f7f7f7;
    border-bottom: 2px solid #ffffff; /* Color de Gmail */
  }
  
  .modal-title {
    font-weight: bold;
    color: #333;
  }
  
  .close {
    color: #0c0c0c; /* Color de Gmail */
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .form-group label {
    font-weight: bold;
    color: #000000;
  }
  
  .form-control {
    border-radius: 4px;
    transition: border-color 0.3s;
  }
  
  .form-control:focus {
    border-color: #2e7d32; /* Color más oscuro al enfocar */
    box-shadow: 0 0 5px #2e7d32;
  }
  
  .btn-primary {
    background-color: #2e7d32; /* Color de Gmail */
    border: none;
    transition: background-color 0.3s;
  }
  
  .btn-primary:hover {
    background-color: #2e7d32; /* Color más oscuro al pasar el mouse */
  }
  
  .boton-gmail-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 0; /* Ajusta el margen según sea necesario */
  }
  
  .btn-gmail {
    background-color: #2e7d32; /* Color rojo de Gmail */
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s; /* Transiciones suaves */
  }
  
  .btn-gmail svg {
    margin-right: 8px; /* Espaciado entre el icono y el texto */
  }
  
  .btn-gmail:hover {
    background-color: #2e7d32; /* Color más oscuro al pasar el mouse */
    transform: scale(1.05); /* Efecto de escala al pasar el mouse */
  }
  
  .btn-gmail:focus {
    outline: none; /* Elimina el contorno por defecto */
  }
  
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
    background-color: #ffffff;
    color: #fff;
  }
  .modal-footer {
    background-color: #f9f9f9;
  }
  
  .container {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .card {
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
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
  
  
  .table-wrapper {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    max-height: 900px; 
    overflow-x: auto; 
  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .data-table th,
  .data-table td {
    padding: 14px;
    text-align: center;
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
    background-color: #f8f9fa;
  
  }

  #modal2{
    margin-top: -300px;
  }
  
  .modal-header {
    border-bottom: 1px solid #ddd;
  }
  
  .modal-title {
    color: #333;
    font-weight: bold;
  }
  
  .modal-body {
    padding: 20px;
    background: white;
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
  #ActualizarClientes{
    background: #2e7d32;
    color: white; 
  }
  #ContenidoModalCliente{
    margin-top: -320px;
    border-radius: 20px;
  }
  #formclientes{
    border-radius: 20px;
  }
  #CuerpoDePago{
    margin-top: -100px;
    border-radius: 20px;
  }
  #BotonDeAbono{
    background: #2e7d32;
    color: white; 
  }
  table {
    background-color: white;
    border-collapse: collapse;
    border: none;
  }

  table th, table td {
      border: none;
      padding: 8px;
      text-align: center;
  }

  #CuerpoProductos{
    width: 900px;
    height: auto;
    position: absolute;
    top: 0%; 
    left: -30%; 
  }
  #botondedeudas{
    background: #2e7d32;
    color: white; 
  }

  #CuerpoProductosDeudas{

    max-height: 800px; 
    overflow-y: auto; 
  }
  .modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.left-text {
    display: flex;
    flex-direction: column;
    font-size: 20px;
}

.left-text span {
    margin-bottom: 10px;
}

.abono, .debe {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.right-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.right-buttons button {
    margin-left: 10px;
}


  </style>