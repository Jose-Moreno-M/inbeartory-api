<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // Metodo para obtener todos los usuarios registrados
    public function getUsers()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    // Metodo para editar un usuario
    public function editUser(Request $request, $user_id)
    {
        // Validación de los datos
        $validator = validator($request->all(), [
            'user_name' => 'string|max:255',
            'email' => 'required|string|email|max:255',
            'curp' => 'required|string|max:18',
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

        // $password = User::where('id', '=', $user_id)->get();
        $data['user_name'] = $request->json('user_name');
        $data['email'] = $request->json('email');
        // $data['password'] = Hash::make(($password)); // Encriptar la contraseña
        $data['curp'] = $request->json('curp');
        $data['name'] = $request->json('name');
        $data['user_role'] = $request->json('user_role');
        $data['position'] = $request->json('position');

        $user = User::findOrFail($user_id);
        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User edited successfully',
            'user' => $user,
        ]);
    }

    // Metodo para eliminar un usuario
    public function deleteUser($user_id)
    {
        $user = User::findOrFail($user_id);

        if ( $user->delete()){
            return response()->json(['success' => $user], 200);
        }else{
            return response()->json('error', 500);
        }
    }
}
