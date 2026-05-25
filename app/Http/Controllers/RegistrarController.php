<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    /**
     * Muestra la vista de registro.
     *
     * @return \Illuminate\View\View
     */

  
    public function regis(): \Illuminate\View\View
    {
        return view('auth.registrar');
    }

    /**
     * Maneja la solicitud para registrar un nuevo usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear un nuevo usuario
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Redirigir al usuario al login con un mensaje de éxito
        return redirect()->route('login')->with('success', 'Usuario registrado exitosamente. Por favor, inicia sesión.');
    }


}

