<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    // Metodo get para obtener todo las areas registrados.
    public function getAreas()
    {
        //$areas = Area::all()->take(10);
        $areas = DB::table('areas')
            ->join('users', 'areas.user_id', '=', 'users.id')
            ->select('areas.*', 'users.name as user_name')
            ->get();

        return ($areas->isNotEmpty()) ? response()->json(['areas' => $areas], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
    }
    // Metodo get para obtener todo las areas registrados,muestra los 10 primeros resultados.
    public function getUserAreas($user_id)
    {
        //$areas = Area::all()->take(10);
        $areas = DB::table('areas')
            ->select('areas.*')
            ->where('areas.user_id', '=', $user_id)
            ->get();

        return ($areas->isNotEmpty()) ? response()->json(['areas' => $areas], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
    }
    // Metodo para crear una nueva area.
    public function createArea(Request $request)
    {
        // Validación de los datos
        $validator = validator($request->json()->all(), [
            'area_name' => 'required|string|max:255|unique:areas',
            'building' => 'required|integer|max:10',
            'floor' => 'required|integer|max:10',
            'wing' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $area = Area::create($request->json()->all());
        if ($area) {
            return response()->json(['success' => $area], 201);
        } else {
            return response()->json(['error' => 'Error al crear'], 403);
        }
    }

    //Metodo para editar un área.
    public function editArea(Request $request, $id)
    {
        // Validación de los datos
        $validator = validator($request->all(), [
            'area_name' => 'required|string',
            'building' => 'required|integer',
            'floor' => 'required|integer',
            'wing' => 'required|string',
            'user_id' => 'required|integer',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'digits' => 'El campo :attribute debe tener :digits dígitos.',
            'unique' => 'El valor del campo :attribute ya está en uso.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $data['area_name'] = $request->json('area_name');
        $data['building'] = $request->json('building');
        $data['floor'] = $request->json('floor');
        $data['wing'] = $request->json('wing');
        $data['user_id'] = $request->json('user_id');


        $item = Area::findOrFail($id);
        $item->update($data);


        return response()->json(['success' => $item], 200);
    }


    public function deleteArea(Request $request, $id)
    {
        //Validar si se ingreso una causa de baja.
        $validator = validator($request->json()->all(), [
            'reason_delete' => 'required|string|min:10',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'digits' => 'El campo :attribute debe tener :digits dígitos.',
            'unique' => 'El valor del campo :attribute ya está en uso.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',

        ]);
        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $area = Area::findOrFail($id);

        if ($area->delete()) {
            return response()->json(['success' => $area], 200);
        } else {
            return response()->json(['error' => $area], 500);
        }
    }


    public function deleteAreaWithTransfer(Request $request, $delete, $transfer)
    {
        //Validar si se ingreso una causa de baja.
        $validator = validator($request->json()->all(), [
            'reason_delete' => 'required|string|min:10',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'digits' => 'El campo :attribute debe tener :digits dígitos.',
            'unique' => 'El valor del campo :attribute ya está en uso.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',

        ]);
        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $affectedRows = Item::where('area_id', $delete)->update(['area_id' => $transfer]);

        $area = Area::findOrFail($delete);

        if ($area->delete()) {
            return response()->json(['success' => $area], 200);
        } else {
            return response()->json(['error' => $area], 500);
        }
    }
}
