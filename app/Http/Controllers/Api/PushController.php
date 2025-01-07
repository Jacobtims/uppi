<?php

namespace App\Http\Controllers\Api;

use App\Enums\Alerts\AlertType;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class PushController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $personalAccessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (Alert::where('type', AlertType::EXPO)
            ->where('config->token_id', $personalAccessToken->id)
            ->exists()) {
            return response()->json([
                'message' => 'Push notifications already enabled',
            ], 422);
        }

        $alert = Alert::create([
            'type' => AlertType::EXPO,
            'name' => 'Uppi app',
            'destination' => $request->token,
            'is_enabled' => true,
            'user_id' => auth()->id(),
            'config' => [
                'token_id' => $personalAccessToken->id,
            ],
        ]);

        return response()->json([
            'message' => 'Push notifications enabled successfully',
            'alert' => $alert,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        Alert::query()
            ->where('user_id', auth()->id())
            ->where('type', AlertType::EXPO)
            ->where('config->token_id', PersonalAccessToken::findToken($request->bearerToken())->id)
            ->delete();

        return response()->json([
            'message' => 'Push notifications disabled successfully',
        ]);
    }
}
