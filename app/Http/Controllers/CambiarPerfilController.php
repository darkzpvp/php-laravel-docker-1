<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CambiarPerfilController extends Controller
{
    public function subirImagen(Request $request)
    {
        // Obtener el usuario autenticado (si estás utilizando autenticación)
        $usuario = $request->user();
    
        // Verificar si se ha enviado un archivo de imagen
        if ($file = $request->file('imagen')) {
            // Eliminar la imagen de perfil anterior si existe
            if ($usuario->imagen) {
                Storage::disk('public')->delete($usuario->imagen);
            }
    
            // Generar un nombre único para el archivo
            $filename = date('His') . '_' . $file->getClientOriginalName();
    
            // Almacenar el archivo en el sistema de archivos, en la carpeta 'public/images'
            $rutaImagen = $file->storeAs('images', $filename, 'public');
    
            // Actualizar el campo de imagen del usuario con la ruta del archivo almacenado
            $usuario->imagen = $rutaImagen;
            $usuario->save();
    
            return response()->json(['mensaje' => 'Imagen de perfil actualizada con éxito'], 200);
        } else {
            return response()->json(['mensaje' => 'No se ha proporcionado ninguna imagen'], 400);
        }
    }
    public function obtenerImagenPerfil(Request $request)
    {
        // Obtener el usuario autenticado (si estás utilizando autenticación)
        $usuario = $request->user();
        
        // Verificar si el usuario tiene una imagen de perfil
        if ($usuario->imagen) {
            // Construir la URL completa de la imagen de perfil
            $urlImagen = Storage::disk('public')->url($usuario->imagen);
            
            // Verificar si la imagen existe en el almacenamiento
            if (Storage::disk('public')->exists($usuario->imagen)) {
                // Devolver la URL de la imagen de perfil
                return response()->json(['url_imagen' => $urlImagen], 200);
            } else {
                // Si la imagen no existe, establecer el valor de 'imagen' como null y guardar
                $usuario->imagen = null;
                $usuario->save();
                
            }
        } 
    }
}
