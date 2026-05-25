
import './bootstrap';
import { createApp } from 'vue';
import ganancia from "@/components/general/ganancia.vue";
import PrimeVue from 'primevue/config'; // Importa la configuración de PrimeVue
import 'primevue/resources/themes/arya-blue/theme.css'; // Importa el tema de PrimeVue
import 'primevue/resources/primevue.min.css'; // Importa los estilos generales de PrimeVue
import 'primeicons/primeicons.css';
import Menubar from 'primevue/menubar';
import 'primeicons/primeicons.css';
import 'primevue/resources/themes/aura-light-green/theme.css' // Importa los iconos de PrimeIcons

const app = createApp(ganancia); // Crea la aplicación Vue
app.use(PrimeVue); // Instala PrimeVue en la aplicación
app.mount('#ganancia'); // Monta la aplicación en el elemento con el ID "Informacion"