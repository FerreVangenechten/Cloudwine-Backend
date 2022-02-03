<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeatherStation;
use Illuminate\Http\Request;
use App\Models\WeatherStationUser;
use Illuminate\Support\Facades\Validator;

class WeatherStationUserController extends Controller
{
    public function index()
    {
        return WeatherStationUser::all();
    }

    public function show($weather_station_id)
    {
        $weatherStationUser = WeatherStationUser::where('user_id', auth()->id())->where('weather_station_id',$weather_station_id)->first();

        if(auth()->user()->is_superadmin){
            return response()->json($weatherStationUser,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weather_station_id){
                    return response()->json($weatherStationUser,200);
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 403);

    }



    public function update(Request $request,$weather_station_id)
    {
        $weatherStationUser = WeatherStationUser::where('user_id', auth()->id())->where('weather_station_id',$weather_station_id)->first();

        $validator = Validator::make($request->all(), [
            'weather_station_id' => 'required|integer',
            'user_id' => 'required|integer',
            'timeframe_temp' => 'string|nullable',
            'timerframe_hum' => 'string|nullable',
            'timeframe_lux' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($validator->validated()) {
            if (auth()->user()->is_superadmin) {
                $weatherStationUser->update($request->all());
                return response()->json($weatherStationUser, 200);
            } else {
                $userStations = WeatherStation::where('organisation_id', auth()->user()->organisation_id)->get('id');
                foreach ($userStations as $station) {
                    if ($station->id == $weather_station_id) {
                        $weatherStationUser->update($request->all());
                        return response()->json($weatherStationUser, 200);
                    }
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 403);
    }
}
