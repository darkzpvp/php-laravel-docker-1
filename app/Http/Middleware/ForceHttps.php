<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttps
{
    public function handle($request, Closure $next)
    {
        // Verificar si la solicitud no es segura (HTTP)
        if (!$request->secure()) {
            // Redirigir a HTTPS
            return redirect()->secure($request->getRequestUri());
        }

        // Continuar con la solicitud si ya está en HTTPS
        return $next($request);
    }
}
