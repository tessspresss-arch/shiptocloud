<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class AuthenticatedUserController
{
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}

