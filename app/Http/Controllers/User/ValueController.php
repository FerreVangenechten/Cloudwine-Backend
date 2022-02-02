<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller as Controller;

use App\Models\GraphType;
use App\Models\Organisation;
use App\Models\Value;
use App\Models\WeatherStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class ValueController extends Controller
{
    public function index(Request $request, $weather_station_id)
    {
        $sensor = $request->get('sensor');
        $from = date($request->get('start'));
        $to = date($request->get('stop'));

        if(!$from){
            $from = Carbon::now()->subDays(2)->toDateString();
        }
        if(!$to){
            //add one day for the 'inbetween' function
            $to = Carbon::now()->addDays(1)->toDateString();
        }

        $values = Value::where('weather_station_id', $weather_station_id)
            ->with('graphType')
            ->whereBetween('timestamp',[$from,$to])
            ->get();

        if(auth()->user()->is_superadmin){
            if($sensor) {
                $values = Value::where('weather_station_id', $weather_station_id)
                    ->where('graph_type_id',$sensor)
                    ->whereBetween('timestamp',[$from,$to])
                    ->with('graphType')
                    ->orderBy('timestamp')
                    ->get();
            }
            return response()->json($values,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weather_station_id){
                    if($sensor) {
                        $values = Value::where('weather_station_id', $weather_station_id)
                            ->where('graph_type_id',$sensor)
                            ->whereBetween('timestamp',[$from,$to])
                            ->with('graphType')
                            ->orderBy('timestamp')
                            ->get();
                    }
                    return response()->json($values,200);
                }
            }
        }

        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 401);

    }


    public function relais($weather_station_id)
    {
        $status = Value::where('weather_station_id', $weather_station_id)->whereHas('graphType',function($query){
            $query->where('name','SWS');
        })->latest()->first();;

        return response()->json($status,200); //200 --> OK, The standard success code and default option
    }

    public function battery($weather_station_id)
    {
        $status = Value::where('weather_station_id', $weather_station_id)->whereHas('graphType',function($query){
            $query->where('name','BAP');
        })->latest()->first();;

        return response()->json($status,200); //200 --> OK, The standard success code and default option
    }

    public function location($weather_station_id)
    {
        $longitude = Value::where('weather_station_id', $weather_station_id)->whereHas('graphType',function($query){
            $query->where('name','GLO');
        })->with('graphType')->latest()->first();

        $latitude = Value::where('weather_station_id', $weather_station_id)->whereHas('graphType',function($query){
            $query->where('name','GLA');
        })->with('graphType')->latest()->first();;

        if(auth()->user()->is_superadmin){
            return response()->json([$longitude,$latitude],200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weather_station_id){
                    return response()->json([$longitude,$latitude],200);
                }
            }
        }

        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 401);
    }


    public function timeframe(Request $request,$weather_station_id)
    {
        $from = date($request->get('start'));
//        $to = date($request->get('stop'));
        $to = date($request->get('stop'));

        if(!$to){
            //add one day for the 'inbetween' function
            $to = Carbon::now()->addDays(1)->toDateString();
        }

        $values = Value::where('weather_station_id', $weather_station_id)->whereBetween('timestamp',[$from,$to])->with('graphType')->get();


        return response()->json($to,401);
    }
}
