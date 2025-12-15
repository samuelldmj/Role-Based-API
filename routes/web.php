<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'code' => 200,
        'data' => new \stdClass(),
        'message' => 'API is working',
    ], 200);
});
