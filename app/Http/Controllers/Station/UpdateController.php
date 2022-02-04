<?php

namespace App\Http\Controllers\Station;

use App\Http\Controllers\Controller;
use App\Models\Alarm;
use App\Models\WeatherStation;
use App\Models\WeatherStationUpdate;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    public function latestUpdate($weather_station_gsm)
    {
        $weatherstation = WeatherStation::where('gsm',$weather_station_gsm)->first();

        $stationUpdate = WeatherStationUpdate::where('weather_station_id',$weatherstation->id)->with(['otaUpdate' => function($query){
            $query->select(['id','bin_file_path','deploy_on']);
        }])->latest()->first();
        return response()->json($stationUpdate->makeHidden(['ota_update_id','weather_station_id']));

    }

    public function update( Request $request, WeatherStationUpdate $station_update,$weather_station_gsm)
    {
        $weatherstation = WeatherStation::where('gsm',$weather_station_gsm)->first();

        $validator = Validator::make($request->all(), [
            'is_installed' => 'required|boolean',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if ($weatherstation && $validator->validated()){
            $stationUpdates = WeatherStationUpdate::where('weather_station_id',$weatherstation->id)->get('id');
            foreach ($stationUpdates as $stationUpdate){
                if($stationUpdate->id == $station_update->id){
                    $station_update->update($request->all());
                    return response()->json($station_update,200);
                }
            }
            return response()->json([
                'message' => 'Deze update hoort niet bij jouw weerstation',
            ], 401);
        } else {
            return response()->json([
                'message' => 'Deze gsm nummer behoort niet tot een weerstation',
            ], 401);
        }

    }

    public function download(Request $request)
    {
        $path = $request->path;
        return Storage::download($path);
    }
}
