<template>
    <div class="container">
      <!-- Modal -->
      
  
      <!-- Tabla y navegación -->
      <div class="table-wrapper">
        <div class="d-flex justify-content-between">
            <div class="d-flex justify-content-start">
                <!-- Botón para activar el modal (alineado a la izquierda) -->

                <!-- Botones con íconos -->
                <div>
                    <button @click="openDateFilter" style="cursor: pointer;" id="filtar">
                    <i class="fas fa-search"></i>
                    </button>
                    <button @click="generatePdf()" style="border: none; background: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="45" fill="red" class="bi bi-file-pdf" viewBox="0 0 16 16">
                        <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
                        <path d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"/>
                    </svg>
                    </button>
                </div>
                </div>


            <!-- Navegación (alineada a la derecha) -->
            <nav>
                <ul class="pagination" style=" border-radius: 5px; padding: 5px;">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1)" style="color: white;">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="{ active: page === pagination.current_page }">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page" style="color: white;"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1)" style="color: white;">Sig</a>
                    </li>
                </ul>
            </nav>

            </div>

        <br />
         <div id="Tabla">
        <table class="data-table">
          <thead>
            <tr>
              <th>Descripcion</th>
              <th class="center">Precio</th>
              <th class="center">Fecha de Registro</th>
              <th class="center">Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="gasto in gastos" :key="gasto.id_gasto">
            <td style="word-wrap: break-word; white-space: normal; overflow-wrap: break-word; max-width: 300px; word-break: break-word;">
            {{ gasto.descripcion }}
            </td>
              <td class="center">{{ formatCurrency(gasto.precio) }}</td>
              <td class="center">{{ formatDate(gasto.fecha_registro) }}</td>
              <td class="center">
                <div class="btn-container">
                  <button class="btn btn-icon btn-delete" @click="eliminarGasto(gasto.id_gasto)">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          
        </table>
        <div v-if="gastos.length === 0" class="alert alert-warning mt-3 custom-alert text-center mx-auto" style="max-width: 500px;">
            No se encontró ningún Gasto. No se encontró ningún gasto entre estas fechas.
        </div>
       </div>
        <br />
  
        <!-- Botón para mostrar el total de los gastos -->
        <div class="total-container">
          <button class="btn " id="botonGastos" @click="mostrarTotal">Total de Gastos</button>
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
        startDate: '',
        endDate: '',
        descripcion: '',
        precio: '',
        total: 0,
        Total: 0,
        gastos: [],
        pagination: {
          total: 0,
          current_page: 1,
          per_page: 5,
          last_page: 1,
          from: 1,
          to: 5,
        },
        offset: 3,
      };
    },
    computed: {
      pagesNumber() {
        if (!this.pagination.to) return [];
  
        let from = this.pagination.current_page - this.offset;
        if (from < 1) from = 1;
  
        let to = from + (this.offset * 2);
        if (to >= this.pagination.last_page) to = this.pagination.last_page;
  
        let pagesArray = [];
        while (from <= to) {
          pagesArray.push(from);
          from++;
        }
        return pagesArray;
      },
    },
    mounted() {
      this.obtenerGastos();
    },
    methods: {
      cambiarPagina(page) {
        this.pagination.current_page = page;
        this.obtenerGastos(); // Recargar los datos al cambiar de página
      },
      formatearPrecio() {
        let precioFormateado = String(this.precio).replace(/\D/g, '');
        if (precioFormateado.length > 3) {
          precioFormateado = precioFormateado.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        this.precio = precioFormateado ? '$ ' + precioFormateado : '';
      },
      agregarGasto() {
        if (!this.descripcion || !this.precio) {
            Swal.fire('Error', 'Por favor, complete todos los campos', 'error');
            return;
        }

        axios.post('/gastosAgregar', {
            descripcion: this.descripcion,
            precio: this.precio,
        })
        .then(response => {
            Swal.fire({
                title: 'Éxito',
                text: 'Todo salió bien',
                icon: 'success',
                confirmButtonText: 'OK',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.descripcion = '';
                    this.precio = '';
                    this.obtenerGastos(); // No recargues la página, solo actualiza los datos
                }
            });
        })
        .catch(error => {
            Swal.fire('Error', 'Hubo un problema al agregar el gasto', 'error');
            console.error(error);
        });
    },

      obtenerGastos() {
    
        axios.get('/gastosTraer', { params:
             { 
                page: this.pagination.current_page,
                startDate: this.startDate,
                endDate: this.endDate,
             } })
          .then(response => {
            this.gastos = response.data.resultados || [];
            this.Total = response.data.total_gastos;
            this.pagination = response.data.pagination || this.pagination;
            this.calcularTotal();
          })
          .catch(error => {
            Swal.fire('Error', 'Hubo un problema al conectar con el servidor', 'error');
            console.error(error);
          });
      },
      calcularTotal() {
        this.total = this.Total;
      },
      eliminarGasto(id_gasto) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "¡Este gasto será eliminado permanentemente!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
        }).then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/gastoEliminar/${id_gasto}`)
              .then(response => {
                if (response.data.status === 'success') {
                  Swal.fire('Eliminado', response.data.message, 'success');
                  this.obtenerGastos(); // Recargar la lista después de eliminar
                }
              })
              .catch(error => {
                Swal.fire('Error', 'Hubo un problema al eliminar el gasto', 'error');
                console.error(error);
              });
          }
        });
      },
      formatCurrency(value) {
        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
      },
      mostrarTotal() {
        Swal.fire('Total de Gastos', `El total de los gastos es: ${this.formatCurrency(this.total)}`, 'info');
      },
      openDateFilter() {
        let contentHtml = '';
    
        contentHtml = `
            <div class="date-filter-container">
                <label for="start-date">Fecha de inicio:</label>
                <input type="date" id="start-date" class="swal2-input custom-date-input" placeholder="Fecha de inicio" value="${this.startDate || ''}">
                
                <label for="end-date">Fecha de final:</label>
                <input type="date" id="end-date" class="swal2-input custom-date-input" placeholder="Fecha de fin" value="${this.endDate || ''}">
            </div>
        `;

        if (contentHtml) {
            Swal.fire({
                title: 'Filtrar por fecha',
                html: contentHtml, // Aquí se asigna el contenido generado dinámicamente
                showCancelButton: true,
                confirmButtonText: 'Filtrar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    // Obtener las fechas del input
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;

                    // Validación para verificar que ambas fechas sean seleccionadas y que la fecha de inicio no sea mayor que la de finalización
                    if (!startDate || !endDate) {
                        Swal.showValidationMessage('Por favor, selecciona ambas fechas.');
                        return false; // Evita el envío si falta alguna fecha
                    }
                    
                    if (new Date(startDate) > new Date(endDate)) {
                        Swal.showValidationMessage('La fecha de inicio no puede ser posterior a la de finalización.');
                        return false; // Evita el envío si la fecha de inicio es posterior a la de fin
                    }

                    return { startDate, endDate }; // Retorna las fechas para utilizarlas en el then()
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { startDate, endDate } = result.value; 
                    this.startDate = startDate; // Guarda las fechas en el estado del componente
                    this.endDate = endDate; // Guarda las fechas en el estado del componente
                    this.isDateFilterOpened = true; // Indica que el filtro de fechas está abierto
                    this.obtenerGastos();
                } else {
                    // Limpiar fechas si se cancela
                    this.startDate = '';
                    this.endDate = '';
                }
            });
        }
    },
    generatePdf() { 
        const doc = new jsPDF();
        const rows = [];
        const headers = [
            "Descripcion",
            "Precio",
            "Fecha de Registro",
        ];

        doc.setFontSize(18);
        doc.text("Total de Gastos", 14, 15); 

        const totalEarnings = this.Total; 
        doc.setFontSize(12);
        doc.text(`Total de Gastos: ${this.formatCurrency(totalEarnings)}`, 14, 25);

        rows.push(headers);

        this.gastos.forEach(DatosCliente => {
            const row = [
                DatosCliente.descripcion,
                this.formatCurrency(DatosCliente.precio),
                this.formatDate(DatosCliente.fecha_registro)
            ];
            rows.push(row);
        });

        doc.autoTable({
            head: [headers],
            body: rows.slice(1), 
            startY: 40, 
            theme: 'grid',
        });

        doc.output('dataurlnewwindow');
    },
    formatDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
           
                return 'Invalid date';
            }
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },
    },
  };
  </script>
  
  
  


  <style scoped>
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
  th.center, td.center {
  text-align: center; /* Centra el contenido en las celdas */
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
    max-width: 1350px;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
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
    margin-top: -10px;
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
  #Tabla{
    max-height: 600px;
    overflow-y: auto; /* Esto agrega la barra de desplazamiento vertical */
  }
  #filtar{
        height: 40px;
        width: 80px;
        padding: 3px ;
        margin-left: 10px;
        background: #388e3c;
        color: white;
        border-radius: 7px;
        font-size: 18px;
        border: none;
    }
    #botonGastos{
      background: #388e3c;
      color: white;
    }
    .pagination .page-link {
    background: #388e3c !important;
    color: white !important;
    border: none;
    margin: 2px;
}

.pagination .page-item.active .page-link {
    background: #2e7d32 !important;
    font-weight: bold;
}

</style>

