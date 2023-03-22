<?php

interface MiddlewareInterface {
    public function handle(Request $request, Closure $next);
}

class VerifyTokenMiddleware implements MiddlewareInterface {
    public function handle(Request $request, Closure $next) {
        // Prüfen, ob der Token gültig ist
        $token = $request->get('token');
        if ($token !== 'secret') {
            // Wenn der Token ungültig ist, eine Fehlermeldung zurückgeben
            return response('Unauthorized', 401);
        }

        // Wenn der Token gültig ist, die nächste Middleware oder den Handler aufrufen
        return $next($request);
    }
}
