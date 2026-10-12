<?php

namespace NmsApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NmsApp\Support\Branding;

class AuthMeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $role = $user->can('admin') ? 'admin' : 'viewer';

        return response()->json([
            'status' => 'ok',
            'user' => [
                'username' => $user->username ?? $user->email,
                'role' => $role,
            ],
            'app' => Branding::payload(),
        ]);
    }
}
