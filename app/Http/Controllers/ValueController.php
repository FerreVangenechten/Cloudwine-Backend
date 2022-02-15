<?php

namespace App\Http\Controllers;

use App\Models\OTA_Update;
use App\Models\Value;
use App\Models\WeatherStation;
use App\Models\WeatherStationUpdate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index(Request $request, WeatherStation $weatherStation)
    {
        $sensor = $request->get('sensor');
        $from = date($request->get('start'));
        $to = date($request->get('stop'));
//        $values = Value::where('weather_station_id', $weatherStation->id)->with('graphType')->orderBy('timestamp')->get();

        if($to){
            $date = Carbon::parse($to);
            $to = $date->addDay(1)->toDateString();
        }

        if(!$from){
            $from = Carbon::now()->subDays(2)->toDateString();
        }
        if(!$to){
            //add one day for the 'inbetween' function
            $to = Carbon::now()->addDays(1)->toDateString();
        }

        $values = Value::where('weather_station_id', $weatherStation->id)
            ->with('graphType')
            ->whereBetween('timestamp',[$from,$to])
            ->orderBy('timestamp')
            ->get();


        if($weatherStation->is_public && $weatherStation->is_active){
            if($sensor) {
                $values = Value::where('weather_station_id', $weatherStation->id)
                    ->where('graph_type_id',$sensor)
                    ->whereBetween('timestamp',[$from,$to])
                    ->with('graphType')
                    ->orderBy('timestamp')
                    ->get();
            }
            return response()->json($values,200);
        }else {
            return response()->json([
                'message' => 'Data niet beschikbaar',
            ], 403);
        }
    }

    public function relais(WeatherStation $weatherStation)
    {

        $status = Value::where('weather_station_id', $weatherStation->id)->whereHas('graphType',function($query) {
            $query->where('name', 'SWS');
        })->latest()->first();

        if($weatherStation->is_public && $weatherStation->is_active){
            return response()->json($status,200);
        }else {
            return response()->json([
                'message' => 'Data niet beschikbaar',
            ], 403);
        }

    }

    public function battery(WeatherStation $weatherStation)
    {
        $status = Value::where('weather_station_id', $weatherStation->id)->whereHas('graphType',function($query){
            $query->where('name','BAP');
        })->latest()->first();

        if($weatherStation->is_public && $weatherStation->is_active){
            return response()->json($status,200);
        }else {
            return response()->json([
                'message' => 'Data niet beschikbaar',
            ], 403);
        }
    }

    public function location(WeatherStation $weatherStation)
    {
        $longitude = Value::where('weather_station_id', $weatherStation->id)->whereHas('graphType',function($query){
            $query->where('name','GLO');
        })->with('graphType')->latest()->first();

        $latitude = Value::where('weather_station_id', $weatherStation->id)->whereHas('graphType',function($query){
            $query->where('name','GLA');
        })->with('graphType')->latest()->first();;

        if($weatherStation->is_public && $weatherStation->is_active){
            return response()->json([$longitude,$latitude],200);
        }else {
            return response()->json([
                'message' => 'Data niet beschikbaar',
            ], 403);
        }
    }


}
