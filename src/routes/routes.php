<?php

Route::get('activate/{token}', 'Kitano\UserActivation\Controllers\ActivationsController@activate')
     ->name('auth.activate')
     ->middleware('web');

Route::get('/activation/resend', 'Kitano\UserActivation\Controllers\ActivationsController@getResend')
     ->name('auth.reactivate')
     ->middleware('web');

Route::post('/activation/resend', 'Kitano\UserActivation\Controllers\ActivationsController@postResend')
     ->name('auth.reactivate')
     ->middleware('web');
