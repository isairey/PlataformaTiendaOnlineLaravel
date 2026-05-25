
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <title>Registro - Tienda</title>

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #4caf50 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #ConteBase {
            position: relative;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        #TituloKairo h4 {
            color: #2e7d32;
            margin-bottom: 30px;
            font-size: 1.8em;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-group label {
            margin-right: 15px;
            display: flex;
            align-items: center;
        }

        .form-control {
            border: none;
            border-bottom: 2px solid #ccc;
            background: transparent;
            padding: 10px;
            width: 100%;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-bottom: 2px solid #2e7d32;
            background-color: #e8f5e9;
        }

        label svg {
            color: #2e7d32;
        }

        button {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1b5e20;
        }

        .invalid-feedback {
            color: #d32f2f;
            font-size: 0.85em;
            margin-top: 5px;
            text-align: left;
        }

        /* Estilos del botón de login */
        #register-button {
            position: absolute;
            top: 10px;
            left: 10px;
            text-decoration: none;
            background-color: #2e7d32;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #register-button:hover {
            background-color: #1b5e20;
        }

        @media (max-width: 480px) {
            #ConteBase {
                padding: 20px;
            }

            #TituloKairo h4 {
                font-size: 1.5em;
            }

            .form-control {
                font-size: 0.9em;
            }

            button {
                padding: 10px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
<a id="register-button" href="{{ route('login') }}">Login</a>
<main>
    <div id="ConteBase">
        <div id="TituloKairo"><h4>Registro</h4></div>
        <form method="POST" action="{{ route('registrarL') }}">
            @csrf
            <div class="form-group">
                <label for="name">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nombre">
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm3.91 3.093L8 9.707l4.09-2.614L14 8V4L8 7 2 4v4l1.91-1.907z"/>
                    </svg>
                </label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Correo Electrónico">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                        <path d="M8 1a3 3 0 0 0-3 3v3H4.5A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7A1.5 1.5 0 0 0 13 13.5v-5A1.5 1.5 0 0 0 11.5 7H11V4a3 3 0 0 0-3-3zM6 4a2 2 0 0 1 4 0v3H6V4z"/>
                    </svg>
                </label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Contraseña">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                        <path d="M8 15a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm-2.93-6.72L3.24 8.31 4.5 7l2.32 2.29L10.5 5.5l1.32 1.54-4.75 5.4-2.5-3.16z"/>
                    </svg>
                </label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmar Contraseña">
            </div>

            <button type="submit">Registrarse</button>
        </form>
    </div>
</main>
</body>
</html>