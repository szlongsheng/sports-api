<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;
    private int $expire;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? 'default-secret-change-in-production';
        $this->expire = (int) ($_ENV['JWT_EXPIRE'] ?? 7200);
    }

    public function create(array $data): string
    {
        $payload = [
            'iss'  => 'sports-api',
            'iat'  => time(),
            'exp'  => time() + $this->expire,
            'data' => $data,
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}
