<?php

namespace App\Http\Controllers\Station;

use App\Http\Controllers\Controller;
use App\Models\Alarm;
use App\Models\WeatherStation;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function gsm($weather_station_gsm)
    {
        $weatherstation = WeatherStation::where('gsm',$weather_station_gsm);
        $weather_station_id = json_decode($weatherstation->get('id'), true);
        $alarms = Alarm::where('weather_station_id',$weather_station_id)->where('is_relais',1)->with(['graphType' => function($query){
            $query->select(['id','name']);
        }])->get();

        return response()->json($alarms->makeHidden(['id','weather_station_id','graph_type_id','is_relais','is_notification','graphType.id']),200);
    }
}
