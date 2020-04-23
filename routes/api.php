<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => '/v1', 'namespace' => 'Api\v1', 'as' => 'api.'], function () {
    Auth::loginUsingId(1, true);
    Route::resource('organizations', 'OrganizationController', ['except' => ['create', 'edit']]);
    Route::resource('issues', 'IssueController', ['except' => ['create', 'edit']]);
    Route::resource('statuses', 'IssueStatusController', ['except' => ['create', 'edit']]);
});