
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <title>Inicio de Sesión - Supermercado </title>

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #388e3c 100%); /* Blanco a verde más oscuro */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #ConteBase {
            background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco semitransparente */
            padding: 40px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        #TituloKairo h4 {
            color: #2e7d32; /* Verde oscuro */
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-group label {
            margin-right: 10px;
            display: flex;
            align-items: center;
        }
        .form-control {
            border: none;
            border-bottom: 2px solid #2e7d32;
            background: transparent;
            padding: 5px;
            width: 100%;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-bottom: 2px solid #1b5e20;
            background-color: #f1f8e9; /* Color de fondo suave al enfocarse */
        }
        label svg {
            color: #2e7d32;
        }
        button {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #1b5e20;
        }
        .invalid-feedback {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<main>
    <div id="ConteBase">
        <div id="TituloKairo"><h4>Supermercado</h4></div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
            </div>

            <div class="form-group">
                <label for="password">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                        <path d="M8 1a3 3 0 0 0-3 3v3H4.5A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7A1.5 1.5 0 0 0 13 13.5v-5A1.5 1.5 0 0 0 11.5 7H11V4a3 3 0 0 0-3-3zM6 4a2 2 0 0 1 4 0v3H6V4z"/>
                    </svg>
                </label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">
            </div>

            <!-- Mensaje de error para credenciales -->
            @if ($errors->has('email') || $errors->has('password'))
                <div class="error-credentials">
                    <span>
                        <strong>{{ $errors->first() }}</strong>
                    </span>
                </div>
            @endif

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</main>

</body>
</html>
