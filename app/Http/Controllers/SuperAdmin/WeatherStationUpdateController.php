<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller as Controller;

use App\Models\WeatherStationUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WeatherStationUpdateController extends Controller
{

    public function index()
    {
        return WeatherStationUpdate::with(['weatherStation','otaUpdate'])->get();
    }

    public function show($station_id,$update_id)
    {
        $stationUpdate = WeatherStationUpdate::where('weather_station_id',$station_id)->where('ota_update_id',$update_id)->get();
        return response()->json($stationUpdate);
    }

    public function specificUpdate($update_id)
    {
        $stations = WeatherStationUpdate::where('ota_update_id',$update_id)->with('weatherStation')->get();
        return response()->json($stations);
    }

    public function specificStation($station_id)
    {
        $updates = WeatherStationUpdate::where('weather_station_id',$station_id)->with('otaUpdate')->get();
        return response()->json($updates);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ota_update_id' => 'required|integer',
            'weather_station_id' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($validator->validated()){
            $stationUpdate = WeatherStationUpdate::create(array_merge(
                $validator->validated(),
                [
                    'is_installed' => 0,
                ]
            ));
        }
        return response()->json($stationUpdate, 201); //201 --> Object created. Usefull for the store actions
    }

    public function delete(WeatherStationUpdate $stationUpdate)
    {
        $stationUpdate->delete();
        return response()->json(null, 204); //204 --> No content. When action was executed succesfully, but there is no content to return
    }


}
