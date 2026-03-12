<?php
// api/auth/jwt.php
class JWT {
    private static $key = "akipede_secret_@2026"; // No ambiente real, use variáveis de ambiente

    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        
        $payload = json_encode($payload);
        $payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', "$header.$payload", self::$key, true);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return "$header.$payload.$signature";
    }

    public static function decode($token) {
        $parts = explode('.', $token);
        if (count($parts) != 3) return null;
        
        list($header, $payload, $signature) = $parts;
        
        $valid_signature = hash_hmac('sha256', "$header.$payload", self::$key, true);
        $valid_signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($valid_signature));
        
        if ($signature !== $valid_signature) return null;
        
        return json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
    }
}
?>
