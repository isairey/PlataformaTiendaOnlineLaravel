<style>
    .email-container {
        font-family: Arial, sans-serif;
        padding: 20px;
        background-color: #ffffff; /* Color de fondo blanco */
        border: 1px solid #e0e0e0; /* Borde gris claro */
        border-radius: 8px; /* Bordes redondeados */
        max-width: 600px;
        margin: auto;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Sombra sutil */
        text-align: center; /* Centrar texto en todo el contenedor */
    }
    .logo {
        display: block; /* Para centrar el logo */
        max-width: 150px; /* Ajusta el tamaño del logo */
        margin: 0 auto 20px; /* Centra y agrega espacio */
    }
    .message {
        font-size: 16px;
        color: #555; /* Color gris oscuro */
        margin-top: 20px;
        line-height: 1.5; /* Mejora la legibilidad */
    }
    .footer {
        font-size: 12px;
        color: #999; /* Color gris claro */
        margin-top: 30px;
    }
</style>

<div class="email-container">
    <div class="message">
        
        {{ $mensaje }} 
    </div>
    <br>
    <br>
    <div class="footer">
        Gracias,<br>
        Kairos
    </div>
</div>
