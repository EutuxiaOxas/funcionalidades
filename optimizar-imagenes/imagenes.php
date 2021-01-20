<?php

namespace App\Http\Controllers;
use Storage;
use Str;

//Modelo
use App\Imagen; 

// LIBRERIA PARA UTILIZAR (usar composer require intervention/image)
use Image;      
use Illuminate\Http\Request;

class test extends Controller
{
    public function guardarImagen(Request $request) {
        
        //Guardar imagen principal
        $path = $request->file('image')->store('public');
        $file = Str::replaceFirst('public/', '',$path);
        
        //Instanciar Modelo donde se guardara la ruta
        $test = new Imagen();

        //Crear nueva imagen en base a la original guardada a través de la libreria
        $imagen_optimizada = Image::make(Storage::get($path));

        //Redimencionar imagen
        $imagen_optimizada->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        //Crear un nombre aleatorio con la extensión 
        $portada = Str::random(50).".webp";

        // Guardar nueva imagen optimizada con un 70% de la calidad
        Storage::disk('public')->put($portada, (string) $imagen_optimizada->encode('jpg', 70));

        // Guardar ruta de lasimagenes
        $test->imagen = $file;
        $test->portada = $portada;
        $test->save();

        return back();
    }
}
