<template>
  <br>
  <div id="todonav">
    <div id="ContenedorNav">
      <div class="card">
        <nav class="navbar navbar-expand-lg">
          <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="/Inventario" style="color: black; text-decoration: none;">Inventario</a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="justify-content-center" id="navbarNav">
              <ul class="navbar-nav">
                <li class="nav-item mx-3">
                  <a class="nav-link fw-medium" href="/RegistrarProductos">
                    <i class="fas fa-plus-circle me-2"></i> Registrar Producto
                  </a>
                </li>
                <li class="nav-item mx-3">
                  <a class="nav-link fw-medium" href="/Agregar">
                    <i class="fas fa-box-open me-2"></i> Agregar Productos-Almacén
                  </a>
                </li>
                <li class="nav-item mx-3">
                  <a class="nav-link fw-medium" href="/Ventas">
                    <i class="fas fa-shopping-cart me-2"></i> Ganancias
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>

  <div class="container-fluid mt-4">
    <div class="card shadow-lg">
      <div class="card-body">
        <form @submit.prevent="registrar" class="mt-4">
          <!-- Checkbox para Peso -->
          <div class="col-md-12">
            <div class="form-check">
              <input 
                type="checkbox" 
                class="form-check-input" 
                id="pesoCheckbox" 
                v-model="sePuedePesar">
              <label class="form-check-label text-dark" for="pesoCheckbox">¿El producto se puede pesar?</label>
            </div>
          </div>
          <br>

          <div class="row">
            <!-- Nombre del Producto -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="marca" class="text-dark">Nombre del Producto</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-copyright"></i></span>
                  </div>
                  <input 
                    type="text" 
                    class="form-control form-control-lg custom-input" 
                    id="marca" 
                    v-model="nombre" 
                    required 
                    autocomplete="off">
                </div>
              </div>
            </div>

            <!-- Referencia -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="referencia" class="text-dark">Referencia</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                  </div>
                  <input 
                    type="text" 
                    class="form-control form-control-lg custom-input" 
                    id="referencia" 
                    v-model="referencia" 
                    required 
                    autocomplete="off">
                </div>
              </div>
            </div>

            <!-- Categoría -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="categoria" class="text-dark">Categoría</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-tags"></i></span>
                  </div>
                  <select 
                    class="form-control form-control-lg custom-input" 
                    id="categoria" 
                    v-model="categoria" 
                    required>
                    <option value="" disabled>Selecciona una categoría</option>
                    <option 
                      v-for="categoria in categorias" 
                      :key="categoria.id_categoria" 
                      :value="categoria.id_categoria">
                      {{ categoria.nombre }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Cantidad -->
            <div class="col-md-6" >
              <div class="form-group">
                <label for="cantidad" class="text-dark">Cantidad</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                  </div>
                  <input 
                    type="number" 
                    class="form-control form-control-lg custom-input" 
                    id="cantidad" 
                    v-model="cantidad" 
                    required 
                    min="1">
                </div>
              </div>
            </div>

            <!-- Unidad de Peso -->
            <div class="col-md-6" v-if="sePuedePesar">
              <div class="form-group">
                <label for="unidad_peso" class="text-dark">Unidad de Peso</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                  </div>
                  <select 
                    class="form-control form-control-lg custom-input" 
                    id="unidad_peso" 
                    v-model="unidad_peso">
                    <option value="" disabled>Selecciona una unidad</option>
                    <option value="kg">Kilogramos (kg)</option>
                    <option value="g">Gramos (g)</option>
                    <option value="mg">Miligramos (mg)</option>
                    <option value="t">Toneladas (t)</option>
                    <option value="lb">Libras (lb)</option>
                    <option value="oz">Onzas (oz)</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="precio" class="text-dark">
                  {{ sePuedePesar ? 'Precio Venta Individual (1 kg)' : 'Precio de Venta( Unidad )' }}
                </label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                  </div>
                  <input 
                    type="number" 
                    class="form-control form-control-lg custom-input" 
                    id="precio" 
                    v-model="precio" 
                    required>
                </div>
              </div>
            </div>
              <!-- Precio de Compra -->
              <div class="col-md-6">
              <div class="form-group">
                <label for="precioC" class="text-dark">  {{ sePuedePesar ? 'Precio de compra (1 kg)' : 'Precio de compra( Unidad )' }}</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                  </div>
                  <input 
                    type="number" 
                    class="form-control form-control-lg custom-input" 
                    id="precioC" 
                    v-model="precioC" 
                    required>
                </div>
              </div>
            </div>

            <!-- Fecha de Vencimiento -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="fecha_vencimiento" class="text-dark">Fecha de Vencimiento</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                  </div>
                  <input 
                    type="date" 
                    class="form-control form-control-lg custom-input" 
                    id="fecha_vencimiento" 
                    v-model="fecha_de_vencimiento" 
                    >
                </div>
              </div>
            </div>
      
          </div>

  

          <button type="submit" class="btn btn-dark btn-lg custom-button mt-4">Registrar</button>
        </form>
      </div>
    </div>
  </div>
</template>


<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      referencia: '',
      categoria: '',
      nombre: '',
      precio: '',
      precioC: '',
      fecha_de_vencimiento: '',
      sePuedePesar: false, 
      cantidad: '', 
      unidad_peso: '',
      peso: '', 
      categorias: [] 
    };
  },
  methods: {
    registrar() {
      let formData = {
        referencia: this.referencia,
        categoria: this.categoria,
        nombre: this.nombre,
        precio: this.precio,
        precioC: this.precioC,
        fecha_de_vencimiento: this.fecha_de_vencimiento,
        unidad_peso: this.unidad_peso, 
        sePuedePesar: this.sePuedePesar, 
      };

      if (this.sePuedePesar && this.peso) {
        formData.peso_producto = this.peso; 
      } else {
        formData.cantidad = this.cantidad; 
      }

      axios
        .post('/Registrar', formData)
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: response.data.message || 'Producto registrado correctamente',
          });
          this.limpiarFormulario();
        })
        .catch(error => {
          console.error('Error al guardar los datos:', error.response?.data || error.message);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'No se pudo registrar el producto',
          });
        });
    },

    limpiarFormulario() {
      this.referencia = '';
      this.categoria = '';
      this.nombre = '';
      this.precio = '';
      this.precioC = '';
      this.fecha_de_vencimiento = '';
      this.cantidad = '';
      this.unidad_peso = '';
      this.peso = '';
    },

    obtenerCategorias() {
      axios.get('/obtenerCategorias')
        .then(response => {
          this.categorias = response.data; 
        })
        .catch(error => {
          console.error('Error al obtener la lista de categorías:', error);
        });
    },
  },
  mounted() {
    this.obtenerCategorias();
  }
};
</script>

<style scoped>
/* Separador de línea */
.line-separator {
  border-top: 2px solid #000000;
  width: 100%;
  margin: 0 auto;
}

/* Estilos del contenedor */
.container-fluid {
  max-width: 90%;
  padding: 15px; /* Espaciado uniforme */
  border-radius: 10px;
  background-color: white;
  margin-top: -40px;
}

/* Ajustes del cuerpo de la tarjeta */
.card-body {
  padding-left: 30px;
  padding-right: 30px;
}

/* Estilo del formulario */
form {
  margin-top: -40px;

}

/* Contenedor de entrada personalizada */
.input-container {
  display: flex;
  align-items: center;
  margin-bottom: 10px; /* Espacio debajo de los inputs */
}

.input-container i {
  color: #28a745; /* Color del icono verde */
  margin-right: 10px; /* Espacio entre el icono y el input */
}

.custom-input {
  background-color: transparent;
  border: none;
  border-bottom: 2px solid #28a745; /* Línea verde */
  padding: 10px;
  font-size: 1rem;
  color: #333;
  transition: border-color 0.3s ease;
  width: 100%;
}

.custom-input:focus {
  outline: none;
  border-color: #218838; /* Verde más oscuro cuando el input está enfocado */
}

.custom-button {
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 10px 20px;
  font-size: 1rem;
  transition: background-color 0.3s;
  cursor: pointer;
}

.custom-button:hover {
  background-color: #218838;
}

#card {
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
  margin-top: -30px;
  height: auto;
  font-size: 16px;
  background-color: #ffffff;
  
}

#ContenedorNav {
  width: 62%;
  height: 75px;
  margin-left: 75px;
  margin-bottom: -20px;
}

.card {
  border-radius: 10px;
  overflow: hidden;
  background-color: #ffffff;
}

.card-header {
  color: white;
  font-size: 1.25rem;
  font-weight: bold;
  padding-left: 0px;
  background-color: #28a745; /* Fondo verde */
}

.navbar {
  border-bottom: 1px solid #e0e0e0;
  text-align: center;
  background-color: transparent;
}

.navbar .nav-link {
  font-size: 1rem;
  font-weight: 500;
  color: #333;
  transition: color 0.3s ease, border-bottom 0.3s ease;
  position: relative;
  padding-bottom: 5px;
}

.navbar .nav-link::after {
  content: "";
  position: absolute;
  width: 0;
  height: 2px;
  background-color: #28a745; /* Verde oscuro */
  bottom: 0;
  left: 0;
  transition: width 0.3s ease;
}

.navbar .nav-link:hover::after {
  width: 100%;
}

.navbar .nav-link:hover {
  color: #28a745;
  text-decoration: none;
}

.navbar .nav-item {
  margin-left: 15px;
  margin-right: 10px;
}

.navbar-toggler {
  border-color: transparent;
}

.navbar-toggler-icon {
  display: block;
  width: 25px;
  height: 3px;
  background-color: #333;
  position: relative;
}

.navbar-toggler-icon::before,
.navbar-toggler-icon::after {
  content: "";
  display: block;
  width: 25px;
  height: 3px;
  background-color: #333;
  position: absolute;
  left: 0;
}

.container {
  max-width: 100%;
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

.custom-select {
  position: absolute;
  margin-top: 60px;
  z-index: 1000;
  width: 100%;
  max-height: 150px;
  overflow-y: auto;
  background-color: white;
}

/* Estilo del checkbox */
.form-check-label {
  color: #333; /* Color del texto del checkbox */
}

.form-check-input:checked {
  background-color: #218838; /* Verde oscuro cuando está seleccionado */
  border-color: #218838;
}

.form-check-input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(33, 136, 56, 0.25); /* Sombra al enfocar */
}
</style>
