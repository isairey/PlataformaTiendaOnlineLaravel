<template>
    <div v-if="user.rol === 0"><br>
     <div  class="access-denied">
           <h1>Acceso Denegado</h1>
             <p>No tienes permisos para acceder a este módulo.</p>
      </div>
     </div>
    <div id="cabeza" v-else>  
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
                <div class="card" id="jhk">
                    <div id="app">
                        <button @click="openDateFilter" style="cursor: pointer;" id="filtar">
                            <i class="fas fa-search"></i>
                        </button>
                        <button  @click="generatePdf()"  style="border: none; background: none; ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="45" fill="red" class="bi bi-file-pdf" viewBox="0 0 16 16">
                            <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
                            <path d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"/>
                        </svg>
                        </button>
                    
                        <div class="total-container" v-if="isDateFilterOpened">
                            <span class="total-label">Total</span>
                            <span class="total-amount">{{ formatCurrency(totalPrecio) }}</span>
                        </div>
                        <div class="total-container" v-else>
                            <span class="total-label">Total</span>
                            <span class="total-amount">{{ formatCurrency(totalGeneral) }}</span>
                        </div>

                    </div>

               </div>

            </div>
          </div>
          <br>
          <div id="body">
            <div id="table">
                <table class="table">
                    <thead id="tableth">
                    <tr>
                    
                        <th scope="col">Nombre</th>
                        <th scope="col">Documento</th>
                        <th scope="col">Total</th>
                        <th scope="col">Métodos de pago</th>
                        <th scope="col">Fecha de compra</th>
                    </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <tr  v-for="DatosCliente in Facturas2" :key="DatosCliente.id">
        
                    <td>{{ DatosCliente.nombre_cliente }}</td>
                    <td>{{ DatosCliente.documento }}</td>
                    <td>{{ formatCurrency(DatosCliente.Total_G) }}</td>
                    <td>
                    {{
                        DatosCliente.mp === 1 ? 'Pago en efectivo' :
                        DatosCliente.mp === 2 ? 'Pago en transferencia' :
                        DatosCliente.mp === 3 ? 'Pago con crédito' :
                        DatosCliente.mp === 4 ? 'Crédito pago' :
                        DatosCliente.mp === 10 ? 'Devolucion' :
                        'Tipo de pago desconocido'
                    }}
                    </td>
                    <td>{{ formatDate(DatosCliente.fecha_de_creacion) }}</td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="Facturas2.length === 0" class="alert alert-warning mt-3 custom-alert">
                    No se encontró ninguna factura.
                </div>
                <br>
                <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, )">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page, )" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, )">Sig</a>
                    </li>

                </ul>
             </nav>  
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
 
 jsPDF.autoTable = autoTable;
 
 export default {
     data() {
         return {
            user:{},
             Facturas: [],
             Facturas2: [],
             startDate: '',
             endDate : '',
             totalGeneral: 0,
             totalPrecio: 0,
             stock: 0,
             pagination: {
                 'total': 0,
                 'current_page': 0,
                 'per_page': 0,
                 'last_page': 0,
                 'from': 0,
                 'to': 0,
             },
             offset: 3,
             isDateFilterOpened: false,
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
      
        formatDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
           
                return 'Invalid date';
            }
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },
        formatCurrency(amount) {  // Format Colombian currency
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
        },
        cambiarPagina(page) {
        this.pagination.current_page = page; // Actualiza la página actual
        this.SacarGanancias(this.startDate, this.endDate); // Usa las fechas del estado del componente
            },

        openDateFilter() {
        let contentHtml = '';
        if (this.user.rol == 1) {
            contentHtml = `
                <div class="date-filter-container">
                    <label for="start-date">Fecha de inicio:</label>
                    <input type="date" id="start-date" class="swal2-input custom-date-input" placeholder="Fecha de inicio" value="${this.startDate || ''}">
                    
                    <label for="end-date">Fecha de final:</label>
                    <input type="date" id="end-date" class="swal2-input custom-date-input" placeholder="Fecha de fin" value="${this.endDate || ''}">
                </div>
            `;
        }

        // Si hay contenido que mostrar, se abre el modal de SweetAlert.
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

                    // Validación simple para verificar si ambas fechas han sido seleccionadas
                    if (!startDate || !endDate) {
                        Swal.showValidationMessage('Por favor, selecciona ambas fechas.');
                        return false; // Evita el envío si falta alguna fecha
                    }

                    return { startDate, endDate }; // Retorna las fechas para utilizarlas en el then()
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { startDate, endDate } = result.value; 
                    this.startDate = startDate; // Guarda las fechas en el estado del componente
                    this.endDate = endDate; // Guarda las fechas en el estado del componente
                    this.SacarGanancias(startDate, endDate); // Llama a la función con las nuevas fechas
                    this.isDateFilterOpened = true; // Indica que el filtro de fechas está abierto
                }
            });
        }
    },


        SacarGanancias(startDate, endDate) {
        // Para verificar las fechas

            axios.get('SacarGanancias', {
                params: {
                    startDate: startDate, 
                    endDate: endDate,    
                    page: this.pagination.current_page
                }
            })
            .then((response) => {
                let respuesta = response.data;
                this.Facturas2 = respuesta.data;
                this.pagination = respuesta.pagination;
                this.totalGeneral = response.data.totalGeneral;
                this.totalPrecio = response.data.totalPrecio;
                
            })
            .catch((error) => {
            });
        },
        generatePdf() { 
        const doc = new jsPDF();
        const rows = [];
        const headers = [
            "Nombre",
            "Documento",
            "Total",
            "Métodos de pago",
            "Fecha de Compra"
        ];

        doc.setFontSize(18);
        doc.text("Ganancias Total", 14, 15); 

        const totalEarnings = this.totalGeneral; 
        doc.setFontSize(12);
        doc.text(`Total Ganancias: ${this.formatCurrency(totalEarnings)}`, 14, 25);

        rows.push(headers);

        this.Facturas2.forEach(DatosCliente => {
            const row = [
                DatosCliente.nombre_cliente,
                DatosCliente.documento,
                this.formatCurrency(DatosCliente.Total_G),
                DatosCliente.mp === 1 ? 'Pago en efectivo' :
                DatosCliente.mp === 2 ? 'Pago en transferencia' :
                DatosCliente.mp === 3 ? 'Pago con crédito' :
                DatosCliente.mp === 4 ? 'Crédito pago' :
                DatosCliente.mp === 10 ? 'Devolucion' :
                'Tipo de pago desconocido',
                this.formatDate(DatosCliente.fecha_de_creacion)
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

        Mostrarsctok(){
            axios.get('Mostrarsctok', {
            })
            .then((response) => {
            this.stock = response.data;
            })
            .catch((error) => { 
            });
        },
        Rol() {/// sacr roles para ahcer validacion
        axios.get('/Roles', {
        })
        .then((response) => {
        this.user = response.data;
        }).catch((error) => {
        });
  },

     },
     mounted() {
         this.SacarGanancias();    
         this.openDateFilter();
         this.Rol();
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
    width: 60%;
    padding: 5px;
    }
    #body{
    height: 68%;
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
    #nav{
        display: flex;
        margin-bottom: -20px;
    }
    #app{
    width: 390px;
    height: 50px;
    display: flex;
    }
    .total-container {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f8f9fa;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 590px;
    }
    .total-container span:first-child {
    font-weight: bold;
    }
    #filtar{
        height: 40px;
        width: 120px;
        margin-top: 5px;
        padding: 3px ;
        margin-left: 10px;
        background: black;
        color: white;
        border-radius: 7px;
        font-size: 18px;
    }
    #jhk{
        width: 34%;
        margin-left: 270px;
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
    }
    .total-container {
    display: flex;
    justify-content: space-between; 
    font-weight: bold;
    padding: 10px; 
    }
    .total-label {
        margin-right: 40px; 
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
    #end-date{
        margin-left: 50px;
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
    *{
    height: auto;
  }
  .access-denied {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #ffffff; 
    color: #333333; 
    border: 1px solid #e0e0e0; 
    border-radius: 12px; 
    padding: 30px;
    margin: 40px auto;
    max-width: 450px; 
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); 
    transition: box-shadow 0.3s, transform 0.3s; 
  }
  .access-denied:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2); 
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
    background-color: #007bff;
    color: white; 
    border: none; 
    border-radius: 6px; 
    padding: 12px 24px; 
    cursor: pointer; 
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s; 
  }
  .access-denied button:hover {
    background-color: #0056b3; 
    transform: translateY(-1px);
  }


    </style>