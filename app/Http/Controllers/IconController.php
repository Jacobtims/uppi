<?php

namespace App\Http\Controllers;

use App\Models\StatusPageItem;
use Illuminate\Http\Request;

class IconController extends Controller
{
    public function __invoke(StatusPageItem $statusPageItem)
    {
        if(! $statusPageItem->is_showing_favicon) {
            abort(404);
        }

        $domain = $statusPageItem->monitor->domain;
        if(empty($domain)) {
            return redirect('/globe.svg');
        }

        return redirect('https://icons.duckduckgo.com/ip3/' . $domain . '.ico');

//        $favicon = \Cache::remember('favicon-' . $domain, now()->addMinutes(5), function () use ($domain) {
//            return file_get_contents('https://icons.duckduckgo.com/ip3/' . $domain . '.ico');
//        });
//
//        return response($favicon, 200, [
//            'Content-Type' => 'image/x-icon',
//        ]);
    }
}
