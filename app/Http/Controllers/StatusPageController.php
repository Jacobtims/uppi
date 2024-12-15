<?php

namespace App\Http\Controllers;

use App\Models\StatusPage;

class StatusPageController extends Controller
{
    public function show(StatusPage $statusPage)
    {
        if (!$statusPage->is_enabled) {
            abort(404);
        }

        return view('status-page.show', [
            'statusPage' => $statusPage,
        ]);
    }

    public function statusJson(StatusPage $statusPage)
    {
        return response()->json([
            'status' => $statusPage->status,
        ])->header('Access-Control-Allow-Origin', '*');
    }
}
