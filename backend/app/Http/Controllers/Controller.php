<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function perPage(Request $request, int $default = 12, int $max = 50): int
    {
        return min(max($request->integer('per_page', $default), 1), $max);
    }
}
