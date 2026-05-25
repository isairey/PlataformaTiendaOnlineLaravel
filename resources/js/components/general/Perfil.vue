
<template>
  <div>
    <br>
    <div v-if="user.rol === 0" class="access-denied">
      <h1>Acceso Denegado</h1>
      <p>No tienes permisos para acceder a este módulo.</p>
    </div>
    <div v-else>
      <div class="row">
      <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
              <div class="inner">
                  <h2>{{total_productos_vendidos}}</h2>
                  <p>Productos Disponibles en Inventario</p>
              </div>
              <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-box2-fill" viewBox="0 0 16 16">
                  <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4zM15 4.667V5H1v-.333L1.5 4h6V1h1v3h6z"/>
                </svg>
              </div>
          </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success" data-bs-toggle="modal" data-bs-target="#exampleModal" style="cursor: pointer;">
              <div class="inner">
                  <h2>{{cantidad_productos_vendidos_hoy}}</h2>
                  <p>Productos Vendidos Hoy</p>
              </div>
          </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning" data-toggle="modal" data-target="#netProfitModal">
              <div class="inner">
                  <h2>{{ formatCurrency(ganancias_diarias) }}</h2>
                  <p>Ventas Diarias</p>
              </div>
              <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16">
                  <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/>
                </svg>
              </div>
          </div>
      </div>
        <!-- Contenedor que muestra la cantidad total de clientes -->
        <div class="col-lg-3 col-6 cursor-pointer"  data-bs-toggle="modal" data-bs-target="#myModal">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h2>{{ cantidad_clientes }}</h2>
                    <p>Cantidad Total de Clientes</p>
                </div>
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-file-earmark-person" viewBox="0 0 16 16">
                        <path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2v9.255S12 12 8 12s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h5.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
      <div class="card card-dark">
        <div class="card-header">
          <h3 class="card-title">Análisis Mensual de Ganancias</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="chart">
            <canvas id="barChart" style="min-height: 300px; height: 280px; max-height: 250px; max-width: 100%;"></canvas>
          </div>
        </div>
      </div>

<div id="contenedor">
     
</div>
</div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-custom"> <!-- Clase personalizada -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detalles de Productos Vendidos Hoy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="body">
            <div id="table">
                <table class="table">
                    <thead id="tableth">
                    <tr>
                       <th scope="col">Nombre</th>
                        <th scope="col">Referencia</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">UP</th>
                        <th scope="col">Categoria</th>
                    </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <tr  v-for="Producto in VnetasDelDia" :key="Producto.id_productos">
                    <td>{{ Producto.nombre }}</td>
                    <td>{{ Producto.referencia }}</td>
                    <td>{{ formatCurrency(Producto.total) }}</td>
                    <td>{{ Producto.cantidad }}</td>
                    <td v-if="Producto.estado == 1">{{ Producto.UP }}</td>
                    <td v-else>Unidad</td>
                    <td>{{ Producto.categoria_nombre }}</td>
                 
                    </tr>
                    </tbody>
                </table>
                <div v-if="VnetasDelDia.length === 0" class="alert alert-warning mt-3 custom-alert">
                      No se ha realizado ninguna compra.
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-custom1"> 
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Los 10 Mejores Clientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div id="table">
                <table class="table" style="border-collapse: none; width: 100%;">
                    <thead id="tableth">
                        <tr>
                            <th scope="col" style="text-align: center;">#</th>
                            <th scope="col" style="text-align: center;">Nombre</th>
                            <th scope="col" style="text-align: center;">Documento</th>
                            <th scope="col" style="text-align: center; width: 200px;">Cantidad de Productos</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <tr v-for="(Producto, index) in MejoresCliente" :key="Producto.nombre">
                            <td style="text-align: center;">{{ index + 1 }}</td>
                            <td style="text-align: center;">{{ Producto.nombre }}</td>
                            <td style="text-align: center;">{{ Producto.documento }}</td>
                            <td style="text-align: center;">{{ Producto.total_productos }}</td>
                        </tr>
                    </tbody>
                </table>
                <div 
                    v-if="MejoresCliente.length === 0"  
                    class="alert alert-warning mt-3" 
                    style="width: 100%; max-width: 600px; margin: 0 auto; padding: 15px; text-align: center; margin-bottom: 10px;">
                    No existen clientes registrados hasta el momento.
                </div>
            </div>
          </div>
        </div>
    </div>
</div>
</template>

<script>

import axios from 'axios';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

export default {
data() {
    return {
    user:{},
    total_productos_vendidos: '',
    ganancias_diarias: '',
    cantidad_productos_vendidos_hoy: '',
    cantidad_clientes:'',
    VnetasDelDia: [],
    MejorProducto: [],
    PeorProducto: [],
    MejoresCliente: [],
    barChartData: {
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    datasets: [
      {
        label: 'Ganancias Del  Mes',
        backgroundColor: 'rgba(55, 58, 63, 1)',
        borderColor: 'rgba(55, 58, 63, 1)',
        borderWidth: 1,
        data: []
      },

      ]
      },
      barChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true
          },
          y: {
            stacked: true
          }
        }
      }
    };
},
methods: {
  Rol() {/// sacr roles para ahcer validacion
        axios.get('/Rol', {
        })
        .then((response) => {
        this.user = response.data;
        }).catch((error) => {
        });
  },
  Nav(){  //// sacar los datos del nav
    axios.get('/Nav', {
        })
    .then((response) => {
      this.cantidad_productos_vendidos_hoy = response.data.cantidad_productos_vendidos_hoy;
      this.total_productos_vendidos = response.data.total_productos_vendidos;
      this.ganancias_diarias = response.data.ganancias_diarias;
      this.cantidad_clientes = response.data.cantidad_clientes;
      this.VnetasDelDia = response.data.VnetasDelDia;

      }).catch((error) => {
      });
  },
  formatCurrency(amount) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0, // Sin decimales
                maximumFractionDigits: 0  // Sin decimales
            }).format(amount);
  },
  fetchData() {
      axios.get('/getMonthlyData')
        .then(response => {
          this.barChartData.datasets[0].data = response.data.total_mensual;
          this.renderChart();
        })
        .catch(error => {
        });
  },
  renderChart() {
    var barChartCanvas = document.getElementById('barChart').getContext('2d');
    new Chart(barChartCanvas, {
      type: 'bar',
      data: this.barChartData,
      options: this.barChartOptions
    });
  },
  SacarMejor(){
    axios.get('/SacarMejor', {
        })
        .then((response) => {
        this.MejorProducto = response.data;
        }).catch((error) => {
        });
  },
  SacarPeor(){
    axios.get('/SacarPeor', {
        })
        .then((response) => {
        this.PeorProducto = response.data;
        }).catch((error) => {
        });
  },
  MejoresClientes(){
    axios.get('/MejoresClientes', {
        })
        .then((response) => {
        this.MejoresCliente = response.data;
        }).catch((error) => {
        });
  }

   
},
mounted() {
  this.Rol();
  this.Nav();
  this.fetchData();
  this.SacarMejor();
  this.SacarPeor();
  this.MejoresClientes();
}
};
</script>

<style>
* {
    height: auto;
}
.access-denied {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f4fff8; /* Blanco con toque verde */
    color: #2d6a4f; /* Verde oscuro */
    border: 1px solid #c1e1c1; /* Verde claro */
    border-radius: 12px;
    padding: 30px;
    margin: 40px auto;
    max-width: 450px;
    box-shadow: 0 6px 12px rgba(45, 106, 79, 0.1); /* Verde oscuro */
    transition: box-shadow 0.3s, transform 0.3s;
}
.access-denied:hover {
    box-shadow: 0 12px 24px rgba(45, 106, 79, 0.3); /* Más intenso en hover */
    transform: translateY(-2px);
}
.access-denied h1 {
    font-size: 26px;
    font-family: 'Arial', sans-serif;
    margin-bottom: 15px;
}
.access-denied p {
    font-size: 18px;
    margin-bottom: 25px;
    text-align: center;
}
.access-denied button {
    background-color: #38b000; /* Verde vibrante */
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s;
}
.access-denied button:hover {
    background-color: #2d6a4f; /* Verde más oscuro */
    transform: translateY(-1px);
}
/* CSS para personalizar el modal */
.modal-custom {
    max-width: 800px;
    width: 100%;
}
.modal-body {
    max-height: 600px;
    overflow-y: auto;
}
#table {
    text-align: center;
    font-size: 28px;
    border-collapse: collapse;
    width: 100%;
    height: 68%;
    background-color: #f9fff9; /* Fondo blanco con ligero toque verde */
    color: #2d6a4f; /* Texto en verde oscuro */
    border: none;
}
.table, .table th, .table td {
  border: none;
}
.table-group-divider td {
    text-align: center;
}
#tableth {
    text-align: center;
    border: none;
}
.custom-alert {
    border: 1px solid #38b000; /* Verde vibrante */
    color: #2d6a4f; /* Verde oscuro */
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(56, 176, 0, 0.1); /* Sombra verde */
    margin-top: 10px;
    max-width: 70%;
    text-align: center;
    margin-left: 15%;
    background-color: #f4fff8; /* Fondo blanco con toque verde */
}
#contenedor {
    display: flex;
}
.cursor-pointer {
    cursor: pointer;
}
.modal-custom1 {
    max-width: 1000px;
}
</style>
