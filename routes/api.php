<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use Elastic\Elasticsearch\Client;
use App\Http\Controllers\Api\ProductSearchController;

Route::get('/search/products', ProductSearchController::class);
Route::get('/es/health', function (Client $client) {
    // Snelste check op verbinding / versie
    $resp = $client->info(); // alternatief: $client->cluster()->health();
    return response()->json($resp->asArray());
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);