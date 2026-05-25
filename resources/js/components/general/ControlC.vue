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
                <div id="busqueda" >
                    <div class="form-row">
                        <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="inputCategoria" placeholder="Busca por Referencia" v-model="Referencia" autocomplete="off">
                        
                        </div>
                            <div class="form-group col-md-1">
                                <button type="button" id="butondebuscar" @click="buscar">
                                    <i class="fas fa-search"></i> Buscar Producto
                                </button>

                            </div>
                    </div>
                </div>
            </div>
            <br>
            <div id="table">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Referencia</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">UP</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Estado</th>
                </tr>
                </thead>
                <tbody class="table-group-divider">
                <tr  v-for="DatosCliente in DatosClientesFactura" :key="DatosCliente.orden_servicio">
                    <td>{{ DatosCliente.nombre}}</td>
                    <td>{{ DatosCliente.referencia }}</td>
                    <td>{{ DatosCliente.cantidad }}</td>
                    <td v-if=" DatosCliente.estado == 1">
                        Kg
                    </td>
                    <td v-else>
                        Unidad
                    </td>
                    <td>{{ DatosCliente.categoria_nombre }}</td>
                    <td v-if="DatosCliente.cantidad <= 4">
                        <span class="badge text-bg-danger">Agotado</span>
                    </td>
                    <td v-if="DatosCliente.cantidad > 4 && DatosCliente.cantidad < 5">
                        <span class="badge text-bg-danger">Casi Agotado</span>
                    </td>
                    <td v-if="DatosCliente.cantidad >= 5">
                        <i class="fas fa-check-circle" style="color: green; font-size: 24px; margin-left: 10px;"></i>
                    </td>

                </tr>
                </tbody>
            </table>
                <div v-if="DatosClientesFactura.length === 0" class="alert alert-warning mt-3 custom-alert">
                    No se encontró este producto.
                </div>

            <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, categoriaSelecionada,talla)">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page, categoriaSelecionada,talla)" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, categoriaSelecionada,talla)">Sig</a>
                    </li>

                </ul>
             </nav>
         
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
        DatosClientesFactura: [],
        stock: 0,
        Referencia:'',
        categoriaSelecionada: '',
        Elemento: [],
        pagination: {
                'total'   :   0,
                'current_page'  :0,
                'per_page'   :  0,
                'last_page'   : 0,
                'from'       : 0,
                'to'    : 0,
        },
        offset : 3,
        }; 
    },
    computed: {
        isActived() {
        return this.pagination.current_page;
        },
        pagesNumber() {
        if (!this.pagination.to) {
            return [];
        }

        let from = this.pagination.current_page - this.offset;
        if (from < 1) {
            from = 1;
        }

        let to = from + (this.offset * 2);
        if (to >= this.pagination.last_page) {
            to = this.pagination.last_page;
        }

        let pagesArray = [];
        while (from <= to) {
            pagesArray.push(from);
            from++;
        }
        return pagesArray;
    }

    },
    methods: {
    cambiarPagina(page, Referencia) {
        this.pagination.current_page = page;
        this.Referencia = Referencia;
        this.SacarInforme(); 
    },
    SacarInforme(resetPage = false) {
        if (resetPage) {
            this.pagination.current_page = 1;
        }
        axios.get('SacarInformacioControlC', {
            params: {
                Referencia: this.Referencia,
                page: this.pagination.current_page
            }
        })
        .then((response) => {
            let respuesta = response.data;
            this.DatosClientesFactura = respuesta.resultados;
            this.pagination = respuesta.pagination;
        })
        .catch((error) => {

        });
    },
    buscar() {
        this.SacarInforme(true); // Reinicia la página a la primera al realizar la búsqueda
    },
    formatDate(date) { // sacra fecha normla
            if (!date || isNaN(new Date(date).getTime())) {
           
                return 'Invalid date';
            }

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
    },
    formatCurrency(amount) { /// Funcion sacar preico colombiano $$$
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
    },
    ElementosDeCategoria(categoria,talla){

     axios.get('ElementosDeCategoria', {
        params: {
            categoriaElemento: categoria,
            TallaElemento: talla,
        }
        })
        .then((response) => {
        this.Elemento = response.data;

        })
        .catch((error) => {
          
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
    this.SacarInforme();
    this.Mostrarsctok()
    this.ElementosDeCategoria();
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
    #nav{
        display: flex;
        margin-bottom: -20px;
    }
    .card{
    width: 60%;
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
    #busqueda{
    display: flex;
    width: 47%;
    margin-left: 90px;
    border-radius: 8px;
    margin-bottom: 2px;
    background: #f5f6f9;
    margin-top: 8px;
    }
    #butondebuscar{
        height: 43px;
        width: 200px;
        font-size: 18px;
        background:black;
        margin-left: 50px;
        color: white;
        border-radius: 8px;
    }
    #butondebuscar i{
    font-size: 20px;
    margin-right: 7px;
    }
    .form-group{
    margin-bottom: -40px;
    }
    table{
        text-align: center;
        font-size: 19px;
        border-collapse: collapse;
    }
    .table, .table th, .table td {
    border: none; 
    }
    #reporte-button {
    background-color: black; 
    color: white; 
    padding: 10px 15px; 
    border-radius: 5px; 
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    border: none;
    }
    .form-control{
        height: 45px;
    }
    .custom-alert {
    border: 1px solid #f39c12; 
    color: #e67e22; 
    padding: 15px; 
    border-radius: 5px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    margin-top: 10px; 
    max-width: 50%; 
    text-align: center;
    margin-left: 25%;
    }
    .pagination {
        display: flex;
        justify-content: center;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }
    .page-item {
        margin: 0 0.25rem;
    }
    .page-link {
        color: #fff;
        background-color: rgba(55, 58, 63, 1);
        border: 1px solid rgba(55, 58, 63, 1);
        padding: 0.5rem 0.75rem;
        text-decoration: none;
        border-radius: 0.25rem;
        transition: background-color 0.3s, color 0.3s;
    }
    .page-link:hover {
        background-color: rgba(45, 48, 53, 1);
        color: #fff;
    }
    .page-item.active .page-link {
        color: #fff;
        background-color: rgba(35, 38, 43, 1);
        border-color: rgba(35, 38, 43, 1);
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: rgba(55, 58, 63, 1);
        border-color: rgba(55, 58, 63, 1);
    }
    .modal-custom {
    max-width: 60%; /* Ajusta el tamaño a tu preferencia */
    }
    @media (max-width: 768px) {
    .modal-custom {
        max-width: 90%; 
    }
    }
    .btn-close-custom {
    background-color: black;  
    height: 40px;
    width: 70px;
    font-size: 18px;
    color: white;            
    border: none;            
    border-radius: 5px;     
    cursor: pointer;        
    transition: background-color 0.3s; 
    }
    .btn-close-custom:hover {
    background-color: #333;  
    }
    .modal-body {
    max-height: 500px; 
    overflow-y: auto;  
    }
    .text-danger {
    color: red; 
    font-weight: bold; 
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
    #inputCategoria{
        display: flex;
        margin-left: 90px;
        border-radius: 8px;
        margin-bottom: 2px;
        width: 180px;
        margin-right: 360px;
        margin-left: 10px;
        cursor: pointer;
    }








    </style>