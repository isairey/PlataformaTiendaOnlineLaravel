
<template>
    <br>
      <div class="container mt-4" id="cabezanav">
            <div  class="card" id="bsuqueda">
                <div class="search-container">
                    <input type="text" class="search-input" v-model="Buscar"  placeholder="Buscar por documento">
                    <button class="search-button" @click="Vendidos">
                    <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="card" id="busquedamp">
                <div class="search-container">
                    <select class="search-select form-control" v-model="Seleccionado" @change="Vendidos">
                        <option value="">Seleccionar método</option>
                        <option value="1">Efectivo</option>
                        <option value="2">Transferencia</option>
                        <option value="3">Pago con crédito</option>
                        <option value="4">Crédito pago</option>
                        <option value="10">Devolución</option>
                    </select>
                </div>
        
            </div>
        </div>
  
        <div id="table">
                <table class="table">
                    <thead id="tableth">
                    <tr>
                      <th class="text-center">Nombre</th>
                      <th class="text-center">Documento</th>
                      <th class="text-center">Ganancias</th>
                      <th class="text-center">Estado De Pago</th>
                      <th class="text-center">Fecha de compra</th>
                    </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <tr v-for="ProductoVendido in ProductoVendidos" :key="ProductoVendido.id">
                    <td>{{ ProductoVendido.nombre }}</td>
                    <td>{{ ProductoVendido.documento }}</td>
                    <td>{{ formatCurrency(ProductoVendido.Total_G) }}</td>
                    <td>
                    {{
                        ProductoVendido.mp === 1 ? 'Pago en efectivo' :
                        ProductoVendido.mp === 2 ? 'Pago en transferencia' :
                        ProductoVendido.mp === 3 ? 'Pago con crédito' :
                        ProductoVendido.mp === 4 ? 'Crédito pago' :
                        ProductoVendido.mp === 10 ? 'Devolución' :
                        'Tipo de pago desconocido'
                    }}
                    </td>
                    <td>{{ formatFecha(ProductoVendido.fecha_de_creacion) }}</td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="ProductoVendidos.length === 0" class="alert alert-warning mt-3 custom-alert">
                    No tiene ningún movimiento.
                </div>
                <br>          
          </div>
          <br> 
          <br> 
          <br> 
          <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,Buscar )">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page, Buscar)" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,Buscar )">Sig</a>
                    </li>

                </ul>
            </nav>
    </template>
   <script>
    import axios from 'axios';
    import { format } from 'date-fns';
    import { es } from 'date-fns/locale';
    export default {
      data() {
        return {
         ProductoVendidos: [],
         Buscar: '',
         Seleccionado:'',
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
      mounted() {
        this.Vendidos();
      },
      methods: {
        Vendidos(resetPage = false) {         
          if (resetPage) {         
              this.pagination.current_page = 1;          
          }         

          axios.get('/SacarMovimientos', {              
              params: {                 
                  page: this.pagination.current_page,                 
                  Buscar: this.Buscar,                 
                  Seleccionado: this.Seleccionado,             
              }         
          })         
          .then((response) => {             
              let respuesta = response.data;             
              this.ProductoVendidos = respuesta.data;             
              this.pagination = respuesta.pagination;             

              if (this.ProductoVendidos.length > 0) {                 
                  this.noProductoFound = false;              
              } else {                 
                  this.noProductoFound = true;    

                  if (!resetPage && this.Buscar.trim() !== "") { 
                      Swal.fire({                     
                          icon: 'error',                     
                          title: 'Cliente no encontrado',                     
                          text: `No se encontró ningún movimiento con el documento "${this.Buscar}" ni con el estado de pago "${this.Seleccionado}".`,                     
                          confirmButtonText: 'Aceptar'                 
                      });  
                  }            
              }         
          })         
          .catch((error) => {         
              console.error('Error al obtener movimientos:', error);
          });         
      },


        cambiarPagina(page) {
            this.pagination.current_page = page; // Actualiza la página actual
            this.Vendidos(); // Llama a Vendidos sin reiniciar la búsqueda
        },
        buscar() {
            this.Vendidos(true); // Resetea la página a la primera cuando haces una nueva búsqueda
        },
        formatCurrency(amount) {  // Format Colombian currency
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
        },
        formatFecha(fecha) {
            return format(fecha, "dd 'de' MMMM 'del' yyyy 'a las' hh:mm a", { locale: es });
        }
  
    },
    }
    </script>
    <style>
    .modal-content {
      border-radius: 10px;
    }
    .modal-header {
      background-color: #333;
      color: white;
    }
    .modal-title {
      font-size: 1.25rem;
      font-weight: bold;
    }
    .modal-body {
      background-color:white;
    }
    .form-control {
      border-radius: 5px;
    }
    .form-group label {
      font-weight: bold;
    }
    .btn-primary {
      background-color: #333;
      border-color: #333;
    }
    .btn-primary:hover {
      background-color: #555;
      border-color: #555;
    }
    .close {
      color: white;
    }
    .navbar {
      background: none; 
      box-shadow: none; 
    }
    .navbar-nav {
      gap: 20px; 
    }
    .navbar-nav .nav-link {
      color: black; 
      transition: color 0.3s, border-bottom 0.3s; 
      border-bottom: 2px solid transparent; 
    }
    .navbar-nav .nav-link:hover {
      color: black; 
      border-bottom: 2px solid black; 
    }
    .navbar-toggler {
      border-color: black; 
    }
    .container {
      max-width: 100%;
    }
    .card {
      border-radius: 10px;
      overflow: hidden;
    }
    .card-header {
      background-color: #ffffff;
      color: white;
      font-size: 1.25rem;
      font-weight: bold;
      padding-left: 0px;
    }
    .button-group {
      display: flex;
      flex-direction: column;
      align-items: start;
    }
    #fileUpload {
      display: none; 
    }
    button {
      align-self: start;
    }
    button i {
      margin-right: 5px;
    }
    .alert {
      margin-bottom: 20px;
    }
    .btn-update {
        background-color: black; 
        border: none;
        padding: 8px;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
    }
    .btn-update i {
        color: white; 
        font-size: 18px;
        text-align: center;
      
    }
    .btn-delete {
        background-color: red; 
        border: none;
        padding: 8px;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
    }
    .btn-delete i {
        color: white; 
        font-size: 18px;
        text-align: center; 
    }
    .btn-update:hover {
        background-color: #333; 
    }
    .btn-delete:hover {
        background-color: darkred; 
    }
    .btn-container {
      display: flex;
      gap: 10px; 
      justify-content: center; 
      align-items: center; 
    }
    #card {
    padding: 10px;
    border-radius: 16px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    padding-left: 0px;
    height: 80px;
    width: auto; /* Permite que el ancho del card se ajuste automáticamente */
    max-width: 1000px; /* Fija un tamaño máximo si es necesario */
    font-size: 16px;
    justify-content: flex-start; 
    display: flex; /* Mantiene flexbox para el contenido dentro del card */
  }

#ContenedorNav {
  width: 100%; /* Ocupa todo el ancho disponible */
  max-width: 1200px; /* Limita el tamaño máximo del contenedor */
  height: 80px;
  margin-bottom: 4px;
  display: flex;
  justify-content: flex-start; /* Alinea el contenido a la izquierda */

  box-sizing: border-box; /* Asegura que el padding se mantenga dentro del contenedor */
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
        margin-bottom: 10px;
        margin-top: -30px;
    }
    #bsuqueda {
        width: 27%; 
        margin-right: 0;
        padding: 10px;
        margin-top: -20px;
    }
    .search-container {
    display: flex;
    align-items: center;
    }
    .search-button {
    border: 1px solid transparent; 
    padding: 10px 15px;
    margin-left: 5px;
    border-radius: 4px;
    background-color: transparent; 
    color: rgba(55, 58,63, 1); 
    cursor: pointer;
    display: flex;
    align-items: center;
    }
    .search-button i {
    font-size: 24px;
    }
    .search-button:hover i {
    color: rgba(55, 58,63, 1); 
    }
    .search-input {
    flex: 1; 
    padding: 10px;
    border-radius: 4px;
    font-size: 16px;
    outline: none; 
    border: none;
    }
    .search-input:focus {
    border-color: #0056b3;
    box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
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
    #busquedamp{
        width: 25%;
        padding: 10px;
        margin-right: 0;
        margin-left: auto;
        margin-top: -20px;
    }
    #cabezanav{
        display: flex;
    }
    </style>