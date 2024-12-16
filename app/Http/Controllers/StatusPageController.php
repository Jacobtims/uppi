<?php

namespace App\Http\Controllers;

use App\Models\StatusPage;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function show(StatusPage $statusPage)
    {
        if (! $statusPage->is_enabled) {
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

    public function embed(StatusPage $statusPage, Request $request)
    {
        if (! $statusPage->is_enabled) {
            abort(404);
        }

        $type = $request->get('type', 'all');

        return view('status-page.embed', [
            'statusPage' => $statusPage,
            'type' => $type,
        ]);
    }
}
