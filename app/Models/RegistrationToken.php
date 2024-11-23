<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegistrationToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'expires_at'];

    private $secretKey = 'wsgf5hVrf2&yP9hzQw12@!rgRk1MK6z!g#';

    /**
     * Generate a new token for registration.
     *
     * @return string
     */
    public static function generateToken()
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 2400,
            'mac' => Str::random(32),
            'tag' => ''
        ];

        $token = JWT::encode($payload, (new self)->secretKey, 'HS256');

        self::create([
            'token' => $token,
            'expires_at' => now()->addMinutes(40),
        ]);

        return $token;
    }
}
