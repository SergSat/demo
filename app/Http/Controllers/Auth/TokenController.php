<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationToken;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Token;

class TokenController extends Controller
{
    /**
     * Generate a new token for registration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(): JsonResponse
    {
        $token = RegistrationToken::generateToken();

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}
