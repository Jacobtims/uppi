<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AppTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = PersonalAccessToken::where('activation_code', $request->code)->where('expires_at', '>', now())->first();

        if (!$token) {
            return response()->json(['message' => 'Invalid activation code'], 422);
        }

        $token->forceFill([
            'name' => explode('/', $request->header('User-Agent'))[0],
            'activation_code' => null,
            'expires_at' => now()->addMonths(3),
        ])->save();

        return response()->json(['token' => $token->plainTextToken]);
    }
}
