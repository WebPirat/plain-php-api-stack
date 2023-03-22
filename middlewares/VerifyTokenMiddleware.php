<?php

class VerifyTokenMiddleware {
    public function handle($request, $next) {
        try {
            $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            $matches = [];
            if (preg_match('/^Bearer\s+(.*?)$/', $authorizationHeader, $matches)) {
                $token = $matches[1];
                // Decode the JWT token using the "decode" method of the "JWT" class
                $payload = JWT::decode($token);

                // Verify the payload contains the necessary information
                if (!isset($payload->iss) || $payload->iss !== 'my-app' || !isset($payload->exp)) {
                    throw new Exception('Invalid token');
                }

                // Verify the token expiration time
                $now = time();
                if ($payload->exp <= $now) {
                    throw new Exception('Token has expired');
                }
            }else{
                throw new Exception('Token not found');
            }
        } catch (Exception $e) {
            header('HTTP/1.0 401 Unauthorized');
            exit($e->getMessage());
        }

        // call the next middleware or the route handler
        return $next($request);
    }
}
