<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
//WEATHER STATION
Route::group(['namespace' => 'App\Http\Controllers\Station'], function() {
    //WEATHER STATION
    Route::post('values', 'ValueController@store');
    Route::get('alarms/gsm/{weather_station_gsm}', 'AlarmController@gsm');
    Route::get('switchstate/{weather_station_gsm}','ValueController@state');
    Route::put('switchstate/{weather_station_gsm}','ValueController@stateupdate');
    Route::get('update/{weather_station_gsm}','UpdateController@latestUpdate');
    Route::put('update/{station_update}/{weather_station_gsm}','UpdateController@update');
    Route::get('gsm/{weather_station_gsm}','PhoneController@index');
    Route::get('download','UpdateController@download');
});

//VISITOR
Route::group(['namespace' => 'App\Http\Controllers'], function() {
    Route::get('weatherstations', 'WeatherStationController@public');
    Route::get('weatherstations/{weatherStation}', 'WeatherStationController@publicid');
    Route::get('values/{weatherStation}', 'Controllers\ValueController@index');
    Route::get('values/relais/{weatherStation}', 'ValueController@relais');
    Route::get('values/battery/{weatherStation}', 'ValueController@battery');
    Route::get('values/location/{weatherStation}', 'ValueController@location');

    Route::post('login', 'AuthController@login');
    Route::post('reset', 'User\PasswordController@reset');
});

//LOGGED USER
Route::middleware(['auth'])->prefix('user')->namespace('App\Http\Controllers')->group(function () {
    Route::post('password', 'User\PasswordController@update');
    Route::get('profile', 'User\ProfileController@edit');
    Route::put('profile', 'User\ProfileController@update');
    //LOGIN
    Route::post('/logout','AuthController@logout');
    Route::post('/refresh', 'AuthController@refresh'); //refresh token
    //ORGANISATION
    Route::get('organisation', 'OrganisationController@show');
    //Value
    Route::get('values/{weather_station_id}', 'User\ValueController@index');
    Route::get('values/relais/{weather_station_id}', 'User\ValueController@relais');
    Route::get('values/battery/{weather_station_id}', 'User\ValueController@battery');
    Route::get('values/location/{weather_station_id}', 'User\ValueController@location');
    //WEATHERSTATION
    Route::get('weatherstations/{weatherStation}', 'Admin\WeatherStationController@show');
    Route::get('weatherstations', 'Admin\WeatherStationController@index');
    //WEATHERSTATIONUSER
    Route::get('stationusers/{weather_station_id}', 'WeatherStationUserController@show');
    Route::put('stationusers/{weather_station_id}', 'WeatherStationUserController@update');
    //GRAPHTYPE
    Route::get('types', 'User\GraphTypeController@index');
    //DOWNLOAD
    Route::get('csv/{weather_station_id}', 'User\CsvController@get_csv');
});

//ADMIN
Route::middleware(['auth', 'admin'])->prefix('admin')->namespace('App\Http\Controllers')->group(function () {
    //ORGANISATION
    Route::put('organisation', 'Admin\OrganisationController@update');
    //USER
    Route::get('users', 'Admin\UserController@index');
    Route::post('users', 'Admin\UserController@store');
    Route::get('users/{user}', 'Admin\UserController@show');
    Route::put('users/{user}', 'Admin\UserController@update');
    //ALARM
    Route::get('alarms/station/{weather_station_id}', 'Admin\AlarmController@index');
    Route::get('alarms/{alarm}', 'Admin\AlarmController@show');
    Route::post('alarms', 'Admin\AlarmController@store');
    Route::put('alarms/{alarm}', 'Admin\AlarmController@update');
    Route::delete('alarms/{alarm}', 'Admin\AlarmController@destroy');
    //WEATHERSTATION
    Route::get('weatherstations/{weatherStation}', 'Admin\WeatherStationController@show');
    Route::put('weatherstations/{weatherStation}', 'Admin\WeatherStationController@update');
});

// SUPERADMIN
Route::middleware(['auth', 'superadmin'])->prefix('super')->namespace('App\Http\Controllers')->group(function () {
    //ORGANISATION
    Route::resource('organisations', 'SuperAdmin\OrganisationController');
    //USER
    Route::resource('users', 'SuperAdmin\UserController');
    //WEATHERSTATIONS
    Route::resource('weatherstations', 'SuperAdmin\WeatherStationController');
    //OTA UPDATE
    Route::resource('updates', 'SuperAdmin\OTAController');
    //WEATHER STATION UPDATE
    Route::get('stationupdates', 'SuperAdmin\WeatherStationUpdateController@index');
    Route::get('stationupdates/update/{update_id}', 'SuperAdmin\WeatherStationUpdateController@specificUpdate');
    Route::get('stationupdates/station/{station_id}', 'SuperAdmin\WeatherStationUpdateController@specificStation');
    Route::get('stationupdates/{station_id}/{update_id}', 'SuperAdmin\WeatherStationUpdateController@show');
    Route::post('stationupdates', 'SuperAdmin\WeatherStationUpdateController@store');
    Route::delete('stationupdates/{stationUpdate}', 'SuperAdmin\WeatherStationUpdateController@delete');
    //MAIL
    Route::resource('mails', 'SuperAdmin\MailController');
});



