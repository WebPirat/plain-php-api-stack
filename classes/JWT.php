<?php

class JWT
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function encode($payload)
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $payload = json_encode($payload);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function decode($jwt)
    {
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = explode('.', $jwt);

        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlHeader));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload));
        $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));

        $valid = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true) === $signature;

        if (!$valid) {
            return null;
        }

        $payload = json_decode($payload, true);

        return $payload;
    }
}