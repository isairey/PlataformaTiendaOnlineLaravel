<template>
    <div id="cabeza">
        <div id="Cuerpo">
            <br>
            <div id="nav">
                <div class="card">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a class="nav-link bg-dark" href="/ControlInventario" style="cursor: pointer;">
                                <i class="fas fa-file-invoice"></i>
                                Control de Facturas
                            </a>
                        </li>
                        <li class="nav-item" id="nav-item">
                            <a href="/stock" class="btn  position-relative">
                                Stock 
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{stock}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item" id="nav-item1">
                            <a href="/ControlCantidad" style="color: black; text-decoration: none;">
                              <button class="btn ">Control de cantidad</button>
                            </a>
                        </li>
                        <li class="nav-item" id="nav-item2">
                            <a href="/ganacias" style="color: black; text-decoration: none;">
                              <button class="btn ">Ingresos de Facturación</button>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
          </div>
          <div id="body">
            <div id="body1" class="card">
                <h4 id="titulo1">Estado del Stock de Productos</h4>
                <br>
                <div style="max-height: 600px; overflow-y: auto;" id="d">
                <div v-for="alerta in ProductosStock" :key="alerta.referencia">
                    <div 
                        v-if="alerta.cantidad <= 5" 
                        :class="['stock-alert', { 'urgent': alerta.cantidad <= 3, 'attention': alerta.cantidad > 3 && alerta.cantidad <= 5 }]">
                        
                        <!-- Mostrar mensaje dependiendo de la cantidad -->
                        <template v-if="alerta.cantidad <= 3">
                            <div class="alert-message">
                                <i class="fas fa-cart-plus alert-icon"></i>
                                <span>
                                    <strong>¡Urgente!</strong> Debes pedir más <strong>{{ alerta.nombre }}</strong>, Referencia <strong>{{ alerta.referencia }}</strong>.
                                </span>
                            </div>
                        </template>
                        
                        <template v-else-if="alerta.cantidad > 3 && alerta.cantidad <= 5">
                            <div class="alert-message">
                                <i class="fas fa-exclamation-triangle alert-icon"></i>
                                <span>
                                    <strong>¡Atención!</strong> El/LA <strong>{{ alerta.nombre }}</strong>, Referencia <strong>{{ alerta.referencia }}</strong>, se está agotando.
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>



            </div>
            <div id="body2" class="card">
                <h4 id="titulo2">Productos Agotados</h4>
                <br>
                <div id="tablaAgotadosContainer" style="max-height: 800px; overflow-y: auto;">
                    <table id="tablaAgotados" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Referencia</th>
                                <th>Cantidad</th>
                                <th>UP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="alerta in ProductosStock" :key="`${alerta.referencia}`" >
                                <td v-if="alerta.cantidad <= 2">
                                    <i class="fas fa-times-circle" style="color: red; font-size: 24px;" title="Agotado"></i>
                                </td>
                                <td v-if="alerta.cantidad <= 2">{{ alerta.nombre }}</td>
                                <td v-if="alerta.cantidad <= 2">{{ alerta.referencia }}</td>
                                <td v-if="alerta.cantidad <= 2">{{ alerta.cantidad }}</td>
                                <td v-if="alerta.cantidad <= 2 && alerta.estado === 1">Kg</td>
                                <td v-else-if="alerta.cantidad <= 2">Unidad</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>


                </div>
          </div>
     </div>

    </template>
  <script >
    import axios from 'axios';
    import Swal from 'sweetalert2';

    export default {
    data() {
        return {
         ProductosStock: [],
         stock: 0
        };
    },

    methods: {
        SacrDatos() {
        axios.get('/SacarDatosSemiAgotados', {
        })
        .then((response) => {
        this.ProductosStock = response.data;
        }).catch((error) => {

        });
        },
        Mostrarsctok(){
            axios.get('Mostrarsctok', {
            })
            .then((response) => {
            this.stock = response.data;
            })
            .catch((error) => { 
            });
         }
},
    mounted() {
        this.SacrDatos();
        this.Mostrarsctok();
    }
};
  </script>
    <style >
    *{
    margin: 0px;
    padding: 0px;
    cursor: default;
    }
    .btn {
        border: none;
        background: none; 
        cursor: pointer; 
        padding: 5px 10px; 
        margin: 0 5px; 
        font-size: 1.2rem; 
    }
    .btn-primary {
        color: blue; 
    }
    a {
    text-decoration: none;
    }
    .custom-modal-body {
    width: 58mm;
    margin: 0 auto;
    padding: 0;
    }
    .modal-dialog {
    margin: 2% auto; 
    }
    #cabeza{
    height: 100%;
    width: 100%;
    }
    #Cuerpo{
    width: 100%;
    }
    .card{
    width: 50%;
    padding: 5px;
    }
    #body{
    display: flex;
    height: 88%;
    }
    #body1{
    width: 65%;
    height: 100%;
    margin-right: 30px;
    }
    #body2{
    width: 50%;
    height: 100%;
    }
    #titulo1{
    margin-top: 7px;
    margin-bottom: -10px;
    margin-left: 10px;
    font-family: 'Arial', sans-serif; 
    font-size: 28px;
    font-weight: bold; 
    }
    #titulo2{
    margin-top: 7px;
    margin-bottom: -10px;
    margin-left: 10px;
    font-family: 'Arial', sans-serif; 
    font-size: 28px;   
    font-weight: bold; 
    }
    #stock-alert {
    margin-right: 20px;
    display: flex;
    align-items: center;
    background-color: #e8f7d6;
    color: black;
    padding: 10px;
    font-size: 1rem;
    margin-top: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 30px;
    padding-left: 20px;
    }

    .stock-alert i {
    margin-right: 15px;
    font-size: 1.5rem;
    color: #ffc107;
    }

    .stock-alert strong {
    font-weight: bold;
    color: #333;
    }
    .stock-alert {
    margin-right: 20px;
    display: flex;
    align-items: center;
    background-color: #ffeeba; 
    color: black;
    padding: 10px;
    font-size: 1rem;
    margin-top: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 30px;
    padding-left: 20px;
    }
    .stock-alert i {
    margin-right: 10px;
    font-size: 1.5rem;
    }
    .urgent {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    }
    .urgent i {
    color: #dc3545; 
    }
    .urgent strong {
    color: #721c24;
    }
    .tabla-productos {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    text-align: center;
    }
    .tabla-productos td {
    padding: 10px;
    font-size: 1rem;
    border-bottom: 1px solid #f0f0f0; 
    color: #555;
    text-align: center;
    }
    .tabla-productos tr:last-child td {
    border-bottom: none; 
    text-align: center;
    }
    .btn-inventario {
    background-color: #1f2124;
    color: white;
    padding: 10px 10px; 
    font-size: 1rem;
    cursor: pointer;
    border-radius: 5px;
    display: inline-flex;
    align-items: center; 
    transition: background-color 0.3s ease;
    }
    .btn-inventario:hover {
    background-color:#1f2124;
    }
    .btn-inventario i {
    margin: 0; 
    }
    #d{
        height: 900px;
    }
    #tablaAgotadosContainer {
    max-height: 800px;
    overflow-y: auto; 
    border-radius: 5px; 
    margin: 20px 0; 
    padding: 10px;
    }
    #tablaAgotados {
    width: 100%; 
    border-collapse: collapse; 
    }
    #tablaAgotados thead {
    color: #333; 
    font-weight: bold; 
    }
    #tablaAgotados th, #tablaAgotados td {
    padding: 12px; 
    text-align: center; 
    border-bottom: 1px solid #ddd; 
    }
    #tablaAgotados tbody tr:hover {
        background-color: #f5f5f5; 
    }
    #tablaAgotados td i {
        font-size: 24px; 
    }
    #nav-item {
    width: 60px;
    margin-left: 10px;
    text-align: center;
    padding: 0;
    text-decoration: none;
    }

    #nav-item:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
        color: black;
    }
    #nav-item1:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
    }
    #nav-item2:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
    }

    </style>