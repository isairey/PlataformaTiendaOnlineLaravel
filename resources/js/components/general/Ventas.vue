<template>
    <div class="container">
      <!-- Filtro por Fecha -->
      <div class="filter-container">
        <div class="filter-item">
          <label for="start-date" class="visually-hidden">Fecha de Inicio:</label>
          <input type="date" id="start-date" v-model="startDate" class="form-input" />
        </div>
        <div class="filter-item">
          <label for="end-date" class="visually-hidden">Fecha de Fin:</label>
          <input type="date" id="end-date" v-model="endDate" class="form-input" />
        </div>
        <button @click="filtrarPorFecha" class="btn-filter">
          <i class="fas fa-filter"></i>
        </button>
      </div>
  
      <!-- Tabla -->
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Referencia</th>
              <th>Cantidad en Inventario</th>
              <th>UP</th>
              <th>Cantidad de Venta</th>
              <th>UP de venta</th>
              <th>Ganancias</th>
           
            </tr>
          </thead>
          <tbody>
            <tr v-for="ganancia in ganancias" :key="ganancia.referencia">
              <td>{{ ganancia.nombre }}</td>
              <td>{{ ganancia.referencia }}</td>
              <td>{{ ganancia.cantidad_inventario }}</td>

              <!-- Comprobar si ganancia.up es null -->
              <td v-if="ganancia.up === null">Unidad</td>
              <td v-else>Kilogramos</td>

              <td>{{ ganancia.cantidad_venta }}</td>

              <!-- Comprobar si ganancia.up es null -->
              <td v-if="ganancia.up === null">Unidad</td>
              <td v-else>{{ ganancia.up }}</td>
              <td>{{ formatCurrency(ganancia.ganancias) }}</td>
            </tr>


          </tbody>
          <div id="alerta" class="alerta" v-if="ganancias && ganancias.length === 0">
            <p>No se encontró ninguna ganancia en estos días.</p>
          </div>



        </table>
        <br>
        <div class="total-container">
          <button class="btn btn-success" @click="mostrarTotal">
            <i class="fas fa-dollar-sign"></i>
          </button>
          <button class="btn btn-danger" @click="generatePDF">
            <i class="fas fa-file-pdf"></i>
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  import { jsPDF } from "jspdf";
  import "jspdf-autotable";

  
  export default {
    data() {
      return {
        ganancias: [],
        total: 0,
        startDate: '',
        endDate: ''
      };
    },
    mounted() {
      this.obtenerGanancias();
      this.calcularTotal();
    },
    methods: {
      obtenerGanancias() {
        axios
          .get('/gananciaTraer', {
            params: {
              startDate: this.startDate,
              endDate: this.endDate
            }
          })
          .then(response => {
        if (response.data.status === 'success') {
          this.ganancias = response.data.ganancias;
          console.log('Ganancias:', this.ganancias);  // Asegúrate de que se están recibiendo los datos correctamente
          this.calcularTotal();
        } else {
          console.error('Error al obtener las ganancias:', response.data.message);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.data.message,
          });
        }
      })
     },
  
      formatCurrency(value) {
        return new Intl.NumberFormat('es-CO', { 
          style: 'currency', 
          currency: 'COP',
          minimumFractionDigits: 0,  // Esto elimina los decimales
          maximumFractionDigits: 0   // Esto también asegura que no haya más de 0 decimales
        }).format(value);
      },
      calcularTotal() {
        this.total = this.ganancias.reduce((sum, ganancia) => sum + parseFloat(ganancia.ganancias || 0), 0);
      },
  
      mostrarTotal() {
        Swal.fire({
          title: 'Total de Ganancias',
          text: `El total de las ganancias es: ${this.formatCurrency(this.total)}`,
          icon: 'info',
        });
      },
  
      filtrarPorFecha() {
        this.obtenerGanancias(); // Volver a obtener las ganancias con los filtros de fecha
      },
  
      generatePDF() {
          const doc = new jsPDF();

          // Título
          doc.text('Reporte de Ganancias', 20, 10);

          // Calcular el total de ganancias si no está disponible
          this.total = this.ganancias.reduce((acc, item) => acc + (item.cantidad_venta * item.precio), 0);

          doc.autoTable({
        head: [
            ['Nombre', 'Referencia', 'Cantidad en Inventario', 'UP', 'Cantidad de Venta', 'Ganancias']
        ],
        body: this.ganancias.map(item => [
            item.nombre,
            item.referencia,
            item.cantidad_inventario,
            item.UP === null ? 'Unidad' : 'Kilogramos',  // Condicional para mostrar "Unidad" o "Kilogramos"
            item.cantidad_venta,
            this.formatCurrency(item.ganancias),  // Calcular ganancias
        ]),
        startY: 20,
        headStyles: { halign: 'center' },  // Centrar encabezados
        bodyStyles: { halign: 'center' },  // Centrar celdas
        columnStyles: { 
            0: { halign: 'center' }, // Centrar la primera columna (Nombre)
            1: { halign: 'center' }, // Centrar la segunda columna (Referencia)
            2: { halign: 'center' }, // Centrar la tercera columna (Cantidad en Inventario)
            3: { halign: 'center' }, // Centrar la cuarta columna (UP)
            4: { halign: 'center' }, // Centrar la quinta columna (Cantidad de Venta)
            5: { halign: 'center' }, // Centrar la sexta columna (Ganancias)
        }
    });

          // Total de ganancias
          this.calcularTotal();
          doc.text(`Total de Ganancias: ${this.formatCurrency(this.total)}`, 20, doc.lastAutoTable.finalY + 10);

          // Guardar el PDF
          doc.save('reporte_ganancias.pdf');
      }

    }
  };
  </script>
  
  
    
    
    
  
  
    <style scoped>
  
  .filter-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  
  .filter-item {
    display: flex;
    flex-direction: column;
    width: 30%;
  }
  
  .filter-item label {
    font-weight: bold;
    margin-bottom: 5px;
    visibility: hidden;
  }
  
  .form-input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
  }
  
  .btn-filter {
    background-color: #007bff;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .btn-filter:hover {
    background-color: #0056b3;
  }
  
  .table-wrapper {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .data-table th,
  .data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
  }
  
  .data-table th {
    background-color: #f5f5f5;
    color: #333;
  }
  
  .total-container {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
  }
  
  .btn {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn-success {
    background-color: #28a745;
    color: white;
    border: none;
  }
  
  .btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
  }
  
  .btn:hover {
    opacity: 0.9;
  }
  
  .fas {
    font-size: 18px;
  }
    /* Estilos para el modal */
    .modal-content {
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .modal-header, .modal-footer {
      border: none;
      background-color: #ffffff;
    }
    .modal-title {
      font-weight: bold;
      color: #333;
    }
    .btn-close {
      background: none;
      border: none;
    }
    .btn-primary, .btn-secondary {
      border-radius: 5px;
      padding: 10px 20px;
      transition: background-color 0.3s, transform 0.2s;
    }
    .btn-primary {
      background-color: #4caf50;
      border: none;
      color: white;
    }
    .btn-primary:hover {
      background-color: #388e3c;
      transform: scale(1.05);
    }
    .btn-secondary {
      background-color: #009688;
      color: white;
    }
    .btn-secondary:hover {
      background-color: #00796b;
      transform: scale(1.05);
    }
    /* Estilo de los inputs */
    .form-label {
      font-weight: bold;
      color: #333;
    }
    .form-control {
      border-radius: 5px;
      border: 1px solid #e0e0e0;
      padding: 10px;
      font-size: 1rem;
    }
    .form-control:focus {
      border-color: #4caf50;
      box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }
  
    /* Estilos generales */
    .container {
      width: 100%;
      max-width: 1200px;
      margin: auto;
      padding: 20px;
    
    }
  
    /* Modal */
    .modal-content {
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .modal-header, .modal-footer {
      border: none;
      background-color: #ffffff;
    }
    .modal-title {
      font-weight: bold;
      color: #333;
    }
    .btn-close {
      background: none;
      border: none;
    }
    .btn-primary, .btn-secondary {
      border-radius: 5px;
      padding: 10px 20px;
      transition: background-color 0.3s, transform 0.2s;
    }
    .btn-primary {
      background-color: #4caf50;
      border: none;
      color: white;
    }
    .btn-primary:hover {
      background-color: #388e3c;
      transform: scale(1.05);
    }
    .btn-secondary {
      background-color: #009688;
      color: white;
    }
    .btn-secondary:hover {
      background-color: #00796b;
      transform: scale(1.05);
    }
  
    /* Tabla */
    .table-wrapper {
      margin-top: 20px;
      padding: 20px;
      border-radius: 8px;
      background-color: #ffffff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .table-wrapper:hover {
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    .data-table th, .data-table td {
      text-align: left;
      padding: 12px 15px;
      border-bottom: 1px solid #b2f0b2;
    }
    .data-table th {
      background-color: #ecfcee;
      font-weight: bold;
      color: #333;
    }
    .data-table tbody tr:hover {
      background-color: #f1f1f1;
    }
  
    /* Navegación */
    #nav-item {
      width: 60px;
      margin-left: 10px;
      text-align: center;
      padding: 0;
      text-decoration: none;
      color: #4caf50;
      transition: color 0.3s, border-bottom 0.3s;
    }
    #nav-item:hover {
      border-bottom: 2px solid #4caf50;
      cursor: pointer;
      color: #388e3c;
    }
    #nav-item1:hover,
    #nav-item2:hover {
      border-bottom: 2px solid #4caf50;
      cursor: pointer;
      color: #388e3c;
    }
    /* Estilos generales para la alerta */
    .alerta {
      display: none;  /* Inicialmente oculta */
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #f8d7da;
      color: #721c24;
      padding: 20px;
      border: 1px solid #f5c6cb;
      border-radius: 8px;
      font-size: 16px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 9999;
    }

    /* Estilo para el texto */
    .alerta p {
      margin: 0;
      font-weight: bold;
    }

  </style>