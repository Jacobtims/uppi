<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AppTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = PersonalAccessToken::where('activation_code', strtoupper($request->code))->where('expires_at', '>', now())->first();

        if (! $token) {
            return response()->json(['message' => 'Invalid activation code'], 422);
        }

        DB::beginTransaction();

        if (! (bool) $token->is_persistent) {
            $token->delete();
        }

        $userAgent = explode('/', $request->header('User-Agent'));
        $token = $token->tokenable->createToken($userAgent[0], expiresAt: now()->addYears(2));

        DB::commit();

        return response()->json(['token' => $token->plainTextToken]);
    }
}
