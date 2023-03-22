<?php
// JTW token class in plain php
class JWT
{
    /** Encode a payload into a JWT.
     * @param $payload
     * @param $key
     * @param $algo
     * @return string
     */
    public static function encode($payload, $key, $algo = 'HS256')
    {
        $header = ['typ' => 'JWT', 'alg' => $algo];
        $header = json_encode($header);
        $header = self::urlsafeB64Encode($header);
        $payload = json_encode($payload);
        $payload = self::urlsafeB64Encode($payload);
        $signature = hash_hmac('sha256', "$header.$payload", $key, true);
        $signature = self::urlsafeB64Encode($signature);
        return "$header.$payload.$signature";
    }
    /** Decode a JWT into a PHP object.
     * @param $jwt
     * @param $key
     * @param $algo
     * @return mixed
     * @throws Exception
     */

    public static function decode($jwt, $key, $algo = 'HS256')
    {
        $tokens = explode('.', $jwt);
        if (count($tokens) != 3) {
            throw new Exception('Wrong number of segments');
        }
        list($header64, $payload64, $signature) = $tokens;
        $header = json_decode(self::urlsafeB64Decode($header64));
        $payload = json_decode(self::urlsafeB64Decode($payload64));
        $signatureCheck = hash_hmac('sha256', "$header64.$payload64", $key, true);
        $signatureCheck = self::urlsafeB64Encode($signatureCheck);
        if ($signature != $signatureCheck) {
            throw new Exception('Signature verification failed');
        }
        return $payload;
    }
    private static function urlsafeB64Encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
        return $data;
    }
    private static function urlsafeB64Decode($string)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}