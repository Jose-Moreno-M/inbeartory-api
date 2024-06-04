<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ItemsEliminados;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemsEliminadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // Metodo get para obtener todo los items registrados.
    public function getDeletedItems()
    {
        // $items = Item::all()->take(10);
        $items = DB::table('items_eliminados')
            ->join('areas', 'items_eliminados.area_id', '=', 'areas.id')
            ->select('items_eliminados.*', 'areas.area_name as area_name')
            ->get();

        return ($items->isNotEmpty()) ? response()->json(['items' => $items], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
    }

    // Metodo para obtener los items eliminados por usuario.
    public function getDeletedItemsByUsers($user_id)
    {
        $items = DB::table('items_eliminados')
            ->join('areas', 'items_eliminados.area_id', '=', 'areas.id')
            ->select('items_eliminados.*', 'areas.area_name as area_name')
            ->where('items_eliminados.user_id', '=', $user_id)
            ->get();

        return ($items->isNotEmpty()) ? response()->json(['items' => $items], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
    }
    public function getDeletedItemsByColumn(Request $request)
    {
        // Validación de los datos
        // $validator = validator($request->all(), [
        //     'rows' => 'integer|max:25',
        // ]);

        // // Comprobar si la validación falla
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 422);
        // }

        // Obtener los parámetros del filtro de la solicitud
        $condition = $request->input('condition');
        $area_id = $request->input('area_id');
        $rows = $request->input('rows');
        $state = $request->input('state');
        $user_id = $request->input('user_id');
        $user_role = $request->input('user_role');

        // Inicializar la consulta sin aplicar condiciones
        $query = DB::table('items_eliminados')
            ->join('areas', 'items_eliminados.area_id', '=', 'areas.id')
            ->select('items_eliminados.*', 'areas.area_name as area_name');

        // Aplicar condiciones según los parámetros proporcionados
        if ($condition) {
            $query->where('condition', 'ILIKE', '%' . $condition . '%');
        }

        if ($area_id) {
            $query->where('area_id', $area_id);
        }

        if ($state !== null) {
            $query->where('state', $state);
        }

        // Tomar la cantidad especificada de filas (si se proporciona)
        // if ($rows) {
        //     $query->take($rows);
        // }
        // Filtrar por el ID o ROL del usuario
        if ($user_role == 1) {
            $items = $query->get();
        } else {
            $query->where('items_eliminados.user_id', $user_id);
            $items = $query->get();
        }


        return response()->json(['deleted_items' => $items], 200);
    }
}
