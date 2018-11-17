<?php

Route::middleware('ajax')->group(function () {
    Route::get('/get-available-spaces', 'ParkingController@getAvailableSpaces');

    /**
     * Required parameters
     *  - vehicleId -> String
     */
    Route::post('/check-due-amount', 'ParkingController@checkDueAmount');

    /**
     * Required parameters
     *  - vehicleId -> String
     *  - category -> String
     *
     * Optional parameters
     *  - discountCard -> String
     */
    Route::post('/register-vehicle', 'ParkingController@registerVehicle');

    /**
     * Required parameters
     *  - vehicleId -> String
     */
    Route::post('/un-register-vehicle', 'ParkingController@unRegisterVehicle');
});



