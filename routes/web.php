<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//use App\Odometer;
//use App\Reading;
//use App\Http\Controllers\OdometerController;

// For Authorised users

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function()
    {
        return Redirect::to('auto_park');
    });
    Route::get('getsms/{car_id?}', 'TripController@getSMS');
    Route::get('gps_trackers', 'TripController@gpsTrackerTracking');
    Route::get('gps_map/{car_id?}', 'TripController@routeMap')->name('map');
    Route::post('post_trip', 'TripController@postTrip');
    Route::post('post_start/{start?}', 'TripController@postStartingLocation');
    Route::get('post_received_message/{car_id?}', 'TripController@postReceivedSMS');
    Route::get('auto_park/{car_search?}/{ignition?}', 'CarController@autoPark');
    Route::get('get_car_locations', 'CarController@getCarLocations');
    Route::post('search','CarController@search');
    Route::get('trip_history','TripController@tripHistory');
    Route::get('trip_pdf/{trip_id?}', 'TripController@tripPDF');
    Route::get('remove_car/{car_id}', ['uses' => 'CarController@remove']);
    Route::get('car/{id}', 'CarController@car')->name('car');
    Route::post('/data-entry', 'CarController@dataEntry')->name('car-data-entry');
    Route::get('send_sms', 'TripController@sendSMS');

});


// For Guest
//Route::get('/home', 'HomeController@index');

Auth::routes();
