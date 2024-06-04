<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }
    //Metodo para el inicio de sesion de los usuarios
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success login',
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }
    //Metodo para crear un nuevo usuario en la base de datos del sistema.
    public function register(Request $request)
    {
        // Validación de los datos
        $validator = validator($request->all(), [
            'user_name' => 'string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'curp' => 'required|string|max:18|unique:users',
            'name' => 'required|string|max:255',
            'user_role' => 'required|integer',
            'position' => 'required|string|max:255'
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'size' => 'El campo :attribute debe tener :size dígitos.',
            'unique' => 'El valor del campo :attribute ya está en uso.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'user_name' => $request->json('user_name'),
            'email' => $request->json('email'),
            'password' => Hash::make($request->json('password')), // Encriptar la contraseña
            'curp' => $request->json('curp'),
            'name' => $request->json('name'),
            'user_role' => $request->json('user_role'),
            'position' => $request->json('position')
        ]);

        // $token = Auth::login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            // 'authorisation' => [
            //     // 'token' => $token,
            //     'type' => 'bearer',
            // ]
        ]);
    }

    // Metodo para cerrar sesion
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    //Metodo para obtener al usuario que este autenticado
    public function user()
    {
        return response()->json(Auth::user());
    }

}
