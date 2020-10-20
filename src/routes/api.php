<?php

Route::group(['prefix' => config('resourcefy.prefix')], function () {

    Route::get('referentiels', [\Laranuxt\Resourcefy\Controllers\Api\ReferentielsController::class, 'index']);
    Route::get('cruds', [\Laranuxt\Resourcefy\Controllers\Api\CrudsController::class, 'index']);
    Route::post('cruds', [\Laranuxt\Resourcefy\Controllers\Api\CrudsController::class, 'store']);
    Route::get('cruds/{id}', [\Laranuxt\Resourcefy\Controllers\Api\CrudsController::class, 'show']);
    Route::patch('cruds/{id}', [\Laranuxt\Resourcefy\Controllers\Api\CrudsController::class, 'update']);
    Route::delete('cruds/{id}', [\Laranuxt\Resourcefy\Controllers\Api\CrudsController::class, 'delete']);
    Route::get('login', [\Laranuxt\Resourcefy\Controllers\Api\Auth\LoginController::class, 'login']);
    Route::get('register', [\Laranuxt\Resourcefy\Controllers\Api\Auth\RegisterController::class, 'register']);

});


