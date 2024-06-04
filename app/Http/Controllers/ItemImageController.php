<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemImageController extends Controller
{
    public function show($filename)
    {
        $path = storage_path("app/public/images/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file)->header('Content-Type', $type);
    }
}
