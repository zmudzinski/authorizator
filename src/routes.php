<?php

Route::namespace('\Tzm\Authorizator')->middleware(['web', 'auth'])->group(function () {
    Route::post('authorization/create', 'AuthorizationController@create')->name('authorizator.create'); // Create blank verification
    Route::post('authorization/send', 'AuthorizationController@send')->name('authorizator.send');
    Route::post('authorization/check', 'AuthorizationController@verify')->name('authorizator.check');
});
