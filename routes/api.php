<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',array('prefix' => 'api/v1'), function ($api) {
    $api->get('calulcateDate/{initialDate}/{delay}', 'App\Http\Controllers\Api\CalculatorController@show');
    $api->post('calulcateDate', 'App\Http\Controllers\Api\CalculatorController@show');
});