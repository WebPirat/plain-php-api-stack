<?php

class JWT {
    private $secret;

    public function __construct($secret) {
        $this->secret = $secret;
    }

    public function encode($payload) {
        $header = base64_encode(json_encode(array('alg' => 'HS256', 'typ' => 'JWT')));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payload", $this->secret, true);
        $signature = base64_encode($signature);
        return "$header.$payload.$signature";
    }

    public function decode($jwt) {
        $parts = explode('.', $jwt);
        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        $signature = base64_decode($parts[2]);
        $check = hash_hmac('sha256', "$parts[0].$parts[1]", $this->secret, true);
        if (strcmp($signature, $check) !== 0) {
            return false;
        }
        return $payload;
    }
}