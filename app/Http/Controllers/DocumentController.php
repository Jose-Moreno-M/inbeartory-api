<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Exports\ItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    //Metodo para generar reporte de items en Excel.
    //Metodo para generar reporte de items en Excel.
    public function generateExcelInventoryReport($user_id, $area_id)
    {
        $user = DB::table('users')->select('users.*')->where('users.id', $user_id)->first();
        $user_name = $user->name;
        $user_curp = $user->curp;
        $user_position = $user->position;

        if($area_id != "all"){
            $area = DB::table('areas')->select('areas.*')->where('areas.id', $area_id)->first();
            $area_name = $area->area_name;
            
            $query = DB::table('items')
                ->select('items.*')
                ->where('items.area_id', $area_id);
        } else {
            $area_name = 'Todas las asignadas';
            
            $query = DB::table('items')
                ->select('items.*')
                ->where('items.user_id', $user_id);
        }

        $items = $query->get();

        return Excel::download(new ItemsExport($items, $user_name, $user_curp, $area_name, $user_position), 'Reporte de inventario.xlsx');
    }

    //Metodo para generar reporte de items en PDF.
    public function generatePdfInventoryReport($user_id, $area_id)
    {
        $user = DB::table('users')->select('users.*')->where('users.id', $user_id)->first();
        $user_name = $user->name;
        $user_curp = $user->curp;
        $user_position = $user->position;

        if($area_id != "all"){
            $area = DB::table('areas')->select('areas.*')->where('areas.id', $area_id)->first();
            $area_name = $area->area_name;

            $query = DB::table('items')
                ->select('items.*')
                ->where('items.area_id', $area_id);
        } else {
            $query = DB::table('items')
                ->select('items.*')
                ->where('items.user_id', $user_id);

            $area_name = 'Todas las asignadas';
        }

        $items = $query->get();

        $pdf = Pdf::loadView('exportItemsPdf', compact('items', 'user_name', 'user_curp', 'area_name', 'user_position'))->setPaper('US letter', 'landscape');

        //return $pdf->stream();
        return $pdf->download('Reporte de inventario.pdf');

    }
    
    // public function generatePdfCasualtiesReport($user_id, $area_id){
    //     if($user_id == 0){
    //         $query = DB::table('items_eliminados')
    //             ->select('items_eliminados.*')
    //             ->where('items_eliminados.area_id', $area_id);
    //     } else {
    //         // $user_id = $request->input('user');

    //         // $user = DB::table('users')->select('users.*')->where('users.id', $user_id)->first();

    //         // $user_name = $user->user_name;

    //         $query = DB::table('items_eliminados')
    //             ->select('items_eliminados.*')
    //             ->where('items_eliminados.user_id', $user_id);
    //     }

    //     $items = $query->get();
    //     $user_name = "Ulises Ponce Mendoza";
    //     // $items = Item::all();


    //     $pdf = Pdf::loadView('exportItemsPdf', compact('items', 'user_name'))->setPaper('US letter', 'landscape');

    //     //return $pdf->stream();
    //     return $pdf->download('Reporte de bajas.pdf');
    // }
    
    //  public function generatePdfCasualtiesReport($user_id, $area_id){
    //     $user = DB::table('users')->select('users.*')->where('users.id', $user_id)->first();

    //     if($user_id != 0){
    //         $query = DB::table('items_eliminados')
    //             ->select('items_eliminados.*')
    //             ->where('items_eliminados.area_id', $area_id);
    //     } else {
    //         $query = DB::table('items_eliminados')
    //             ->select('items_eliminados.*')
    //             ->where('items_eliminados.user_id', $user_id);
    //     }

    //     $items = $query->get();
    //     $motive = "motivo de prueba";
    //     $new_area = 3;


    //     $pdf = Pdf::loadView('exportCasualties', compact('items', 'user', 'motive', 'new_area'))->setPaper('US letter', 'landscape');

    //     //return $pdf->stream();
    //     return $pdf->download('Reporte de bajas.pdf');
    // }
    
    public function generatePdfCasualtiesReport($user_id, $area_id, $motive){
        $user = DB::table('users')->select('users.*')->where('users.id', $user_id)->first();

        if($area_id != "all"){
            $area = DB::table('areas')->select('areas.*')->where('areas.id', $area_id)->first();
            $area_name = $area->area_name;
            $query = DB::table('items_eliminados')
                ->select('items_eliminados.*')
                ->where('items_eliminados.area_id', $area_id);
        } else {
            $area_name = "todas las asignadas";
            $query = DB::table('items_eliminados')
                ->select('items_eliminados.*')
                ->where('items_eliminados.user_id', $user_id);
        }

        $items = $query->get();

        $pdf = Pdf::loadView('exportCasualties', compact('items', 'user', 'motive', 'area_name'))->setPaper('US letter', 'portrait');

        //return $pdf->stream();
        return $pdf->download('Reporte de bajas.pdf');
    }
    
    public function uploadHeader(Request $request){
        // Validación de los datos
        $validator = validator($request->all(), [
           'image' => 'required|mimes:jpeg,png,jpg,gif',
       ], [
           'required' => 'El campo :attribute es obligatorio.',
           'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
           'exists' => 'El valor seleccionado para :attribute no es válido.',
       ]);

       // Comprobar si la validación falla
       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 422);
       }

       if ($request->hasFile('image')) {
           $image = $request->file('image');
           $imageName = "header";
           $imagePath = 'images/assets/' . $imageName;
           Storage::disk('public')->put($imagePath, file_get_contents($image));
           $data['image'] = $imageName;

           return response()->json(['image' => $imagePath], 200);
       }else{
        return response()->json(['error' => 'Internal error'], 500);
       }
   }
   
    public function uploadFooter(Request $request){
        // Validación de los datos
        $validator = validator($request->all(), [
        'image' => 'required|mimes:jpeg,png,jpg,gif',
        ], [
        'required' => 'El campo :attribute es obligatorio.',
        'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
        'exists' => 'El valor seleccionado para :attribute no es válido.',
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = "footer";
            $imagePath = 'images/assets/' . $imageName;
            Storage::disk('public')->put($imagePath, file_get_contents($image));
            $data['image'] = $imageName;

            return response()->json(['image' => $imagePath], 200);
        }else{
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
    
    public function uploadHeaderAndFooter(Request $request){
        // Validación de los datos
        $validator = validator($request->all(), [
           'header' => 'mimes:jpeg,png,jpg,gif',
           'footer' => 'mimes:jpeg,png,jpg,gif',
       ], [
           'mimes' => 'El archivo de :attribute debe ser de tipo: :values.',
           'exists' => 'El valor seleccionado para :attribute no es válido.',
       ]);

       // Comprobar si la validación falla
       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 422);
       }

       $res = [];

       if($request->hasFile('header')){
        $header = $request->file('header');
        $headerPath = 'images/assets/header';
        Storage::disk('public')->put($headerPath, file_get_contents($header));
        $res['header'] = $headerPath;
        // return response()->json(['error' => 'Internal error'], 500);
       }
       if($request->hasFile('footer')){
        $footer = $request->file('footer');
        $footerPath = 'images/assets/footer';
        Storage::disk('public')->put($footerPath, file_get_contents($footer));
        $res['footer'] = $footerPath;
        // return response()->json(['error' => 'Internal error'], 500);
       }

        return response()->json($res, 200);
   }
}
