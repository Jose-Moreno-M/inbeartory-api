<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemsEliminados;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Exports\ItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;



class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    //Metodo para probar si la API esta funcionando en internet.
    public function testAPI()
    {
        return response()->json(['success' => 'API Funcionando en la red'], 200);
    }

    // Metodo get para obtener todos los items
    // registrados (SUPERADMIN role_level: 1).
    public function getItems()
    {
        $items = DB::table('items')
            ->join('areas', 'items.area_id', '=', 'areas.id')
            ->select('items.*', 'areas.area_name as area_name')
            ->get();

        return ($items->isNotEmpty()) ? response()->json(['items' => $items], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
    }

    //  Metodo get para obtener todo los items registrados
    //  de acuerdo al usuario que este logeado (USUARIOS ENCARGADOS role_level: 2).
     public function getItemsByUsers(Request $request)
     {
         $items = DB::table('items')
             ->join('areas', 'items.area_id', '=', 'areas.id')
             ->select('items.*', 'areas.area_name as area_name')
             ->where('items.user_id', '=', $request->json('user_id'))
             ->get();

         return ($items->isNotEmpty()) ? response()->json(['items' => $items], 200) : response()->json(['error' => 'No se encontraron resultados'], 500);
     }

    // Metodo para obtener un item por la barra de busqueda.
    public function getItemsBySearch($user_input)
    {

       $items = DB::table('items')
            ->join('areas', 'items.area_id', '=', 'areas.id')
            ->select('items.*', 'areas.area_name as area_name')
            ->where('description', 'LIKE', '%' . $user_input . '%')
            ->orWhere('brand', 'LIKE', '%' . $user_input . '%')
            ->get();

        return ($items->isNotEmpty()) ? response()->json(['items' => $items], 200) : response()->json(['error' => 'No se encontraron resultados relacionados con la busqueda'], 204);
    }

    // Metodo para crear un nuevo item.
    public function createItem(Request $request)
    {
        // Validación de los datos
        $validator = validator($request->all(), [
            'inventory_number' => 'required|string|size:5|unique:items',
            'description' => 'required|string',
            'image' => 'required|mimes:jpeg,png,jpg,gif',
            'brand' => 'required|string',
            'model' => 'required|string',
            'serie' => 'required|string',
            'condition' => 'required|string',
            'comments' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'area_id' => 'required|exists:areas,id',
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
        $data['inventory_number'] = $request->input('inventory_number');
        $data['description'] = $request->input('description');
        $data['brand'] = $request->input('brand');
        $data['model'] = $request->input('model');
        $data['serie'] = $request->input('serie');
        $data['condition'] = $request->input('condition');
        $data['comments'] = $request->input('comments');
        $data['state'] = $request->input('state');
        $data['user_id'] = $request->input('user_id');
        $data['area_id'] = $request->input('area_id');

        if ($request->hasFile('image')) {
            //$timestamp = time();
            $image = $request->file('image');
            //$imageName = $image->getClientOriginalName();
            $imageName = time();
            $imagePath = 'images/' . $imageName;
            Storage::disk('public')->put($imagePath, file_get_contents($image));
            // $imgURL = asset('storage/' . $imagePath);
            $data['image'] = $imageName;

        }


        $item = Item::create($data);
        $item_added = DB::table('items')
                ->select('items.*', 'area.area_name as area_name')
                ->join('areas as area', 'items.area_id', '=', 'area.id')
                ->where('items.id', '=', $item->id)
                ->first();

        return response()->json(['success' => $item_added], 201);
    }

    public function editImage(Request $request, $id){

        // Validación de los datos
        $validator = validator($request->all(), [
            'image' => 'mimes:jpeg,png,jpg,gif',
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imagePath = 'images/' . $imageName;
            Storage::disk('public')->put($imagePath, file_get_contents($image));
            // $imgURL = asset('storage/' . $imagePath);
            $data['image'] = $imageName;

        } else {
            return response()->json(['error' => 'No existe la imagen'], 404);
        }

        $item = Item::findOrFail($id);
        $item->update($data);

        return response()->json(['success' => $item], 200);
    }


    //Metodo para editar un item.
    public function editItem(Request $request, $id)
    {
       // Validación de los datos
        $validator = validator($request->all(), [
            'inventory_number' => 'required|size:5',
            'description' => 'required|string',
            'image' => 'mimes:jpeg,png,jpg,gif',
            'brand' => 'required|string',
            'model' => 'required|string',
            'serie' => 'required|string',
            'condition' => 'required|string',
            'comments' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'area_id' => 'required|exists:areas,id',
        ],[
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
        $data['inventory_number'] = $request->json('inventory_number');
        $data['description'] = $request->json('description');
        $data['brand'] = $request->json('brand');
        $data['model'] = $request->json('model');
        $data['serie'] = $request->json('serie');
        $data['condition'] = $request->json('condition');
        $data['comments'] = $request->json('comments');
        $data['state'] = $request->json('state');
        $data['user_id'] = $request->json('user_id');
        $data['area_id'] = $request->json('area_id');


        $item = Item::findOrFail($id);
        $item->update($data);
        $item_added = DB::table('items')
                ->select('items.*', 'area.area_name as area_name')
                ->join('areas as area', 'items.area_id', '=', 'area.id')
                ->where('items.id', '=', $item->id)
                ->first();

        return response()->json(['success' => $item_added], 200);
    }

    // Metodo para eliminar un item.
    // public function deleteItem(Request $request, $id)
    // {
    //     //Validar si se ingreso una causa de baja.
    //     $validator = validator($request->json()->all(), [
    //         'reason_delete' => 'required|string|min:10',
    //     ],[
    //         'required' => 'El campo :attribute es obligatorio.',
    //         'integer' => 'El campo :attribute debe ser un número entero.',
    //         'digits' => 'El campo :attribute debe tener :digits dígitos.',
    //         'unique' => 'El valor del campo :attribute ya está en uso.',
    //         'string' => 'El campo :attribute debe ser una cadena de texto.',
    //         'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
    //         'exists' => 'El valor seleccionado para :attribute no es válido.',
    //         'min' => 'El campo :attribute debe tener al menos :min caracteres.',

    //     ]);
    //     // Comprobar si la validación falla
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }
    //     $item = Item::findOrFail($id);

    //     $items_eliminados = ItemsEliminados::create([
    //         'reason_delete' => $request->json('reason_delete'),
    //         'inventory_number' => $item->inventory_number,
    //         'description' => $item->description,
    //         'image' => $item->image,
    //         'brand'=> $item->brand,
    //         'model'=> $item->model,
    //         'serie'=> $item->serie,
    //         'condition'=> $item->condition,
    //         'state' => $item->state,
    //         'user_id' => $item->user_id,
    //         'area_id' => $item->area_id,
    //     ]);

    //     if ($item->delete()){
    //         return response()->json(['success' => $item], 200);
    //     }else{
    //         return response()->json(['error' => $item], 500);
    //     }
    // }

    public function deleteItem(Request $request)
    {
        //Validar si se ingreso una causa de baja.
        $validator = validator($request->json()->all(), [
            'reason_delete' => 'required|string|min:10',
            'items' => 'required'
        ],[
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

        $items = $request->input('items');

        $success = '';
        $deleted_items = [];

        foreach ($items as $id){
            $item = Item::findOrFail($id);

            if($item->delete()){
                $success = true;

                $items_eliminados = ItemsEliminados::create([
                    'reason_delete' => $request->json('reason_delete'),
                    'inventory_number' => $item->inventory_number,
                    'description' => $item->description,
                    'image' => $item->image,
                    'brand'=> $item->brand,
                    'model'=> $item->model,
                    'serie'=> $item->serie,
                    'condition'=> $item->condition,
                    'state' => $item->state,
                    'user_id' => $item->user_id,
                    'area_id' => $item->area_id,
                ]);

                array_push($deleted_items, $item);
            } else {
                $success = false;
            }
        }

        if ($success == true){
            return response()->json(['success' => $deleted_items], 200);
        }else{
            return response()->json(['error' => $deleted_items], 500);
        }
    }

    // Metodo para hacer busqueda por filtrado (Columnas).
    public function getItemsByColumn(Request $request)
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
        $query = DB::table('items')
                ->join('areas', 'items.area_id', '=', 'areas.id')
                ->select('items.*', 'areas.area_name as area_name');

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

        // Filtrar por el ID o ROL del usuario
        if($user_role == 1){
            $items = $query->get();
        }else{
            $query->where('items.user_id', $user_id);
            $items = $query->get();
        }




        return response()->json(['Items' => $items], 200);
    }





}
