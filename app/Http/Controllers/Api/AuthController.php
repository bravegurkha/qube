<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(["success" => false,"error" => ["credentials = >""invalid_credentials"],"status" => 401]);
            }
        } catch (JWTException $e) {
            return response()->json(["success" => false,"error" => ["token" => "couldnt_create_token"],"status" => 500]);

        }

        return response()->json(['success' => true, 'data' => compact('token'), 'status' => 200]);
    }

    // somewhere in your controller
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(["success" => false,"error" => ["token" => "token_expired"],"status" => $e->getStatusCode()]);

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(["success" => false,"error" => ["token" => "token_invalid"],"status" => $e->getStatusCode()]);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(["success" => false,"error" => ["token" => "token_absent"],"status" => $e->getStatusCode()]);
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(['success' => true, 'data' => $user, 'status' => 200]);
    }
}
