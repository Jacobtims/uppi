<?php

namespace App\Http\Controllers;

use App\Models\StatusPage;

class StatusPageController extends Controller
{
    public function __invoke(StatusPage $statusPage)
    {
        if (!$statusPage->is_enabled) {
            abort(404);
        }

        return view('status-page.show', [
            'statusPage' => $statusPage,
        ]);
    }
}
