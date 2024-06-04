<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // Metodo para obtener los roles registrados.
    public function getRoles()
    {
        $roles = Roles::all();

        return response()->json(['roles' => $roles], 200);
    }
}
