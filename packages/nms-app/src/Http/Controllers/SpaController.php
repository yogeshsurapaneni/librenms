<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Routing\Controller;

class SpaController extends Controller
{
    public function index()
    {
        $index = public_path('nms/index.html');
        if (! is_file($index)) {
            return response('Custom NMS SPA is not built (html/nms/index.html missing).', 503)
                ->header('Content-Type', 'text/plain');
        }

        return response()->file($index);
    }
}
