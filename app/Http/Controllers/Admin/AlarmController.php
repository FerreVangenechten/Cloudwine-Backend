<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alarm;
use App\Models\WeatherStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlarmController extends Controller
{
    public function index($weather_station_id)
    {
        if(auth()->user()->is_superadmin){
            $alarms = Alarm::where('weather_station_id', $weather_station_id)->with('graphType')->get();
            return response()->json($alarms,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weather_station_id){
                    $alarms = Alarm::where('weather_station_id', $weather_station_id)->with('graphType')->get();
                    return response()->json($alarms,200);
                }
            }
        }
        return response()->json([
            'message' => 'Dit alarm is niet van jouw organisatie',
        ], 403);


    }

    public function show(Alarm $alarm)
    {
        $show = Alarm::where('id',$alarm->id)->with(['weatherStation' => function($query){
            $query->select(['id','relais_name']);
        },'graphType' => function($query1){
            $query1->select(['id','name']);
        }])->get();

        if(auth()->user()->is_superadmin){
            return response()->json($alarm,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $alarm->weather_station_id){
                    return response()->json($show[0],200);
                }
            }
        }
        return response()->json([
            'message' => 'Dit alarm is niet van jouw organisatie',
        ], 403);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weather_station_id' => 'integer',
            'graph_type_id' => 'required|integer',
            'switch_value' => 'required|numeric',
            'operator' => 'required|string',
            'is_relais' => 'required|boolean',
            'is_notification' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(auth()->user()->is_superadmin){
            $alarm = Alarm::create(array_merge(
                $request->all(),[
                'is_email_send' => false
            ]));
            return response()->json($alarm, 201);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $request->weather_station_id){
                    $alarm = Alarm::create(array_merge(
                        $request->all(),[
                            'is_email_send' => 0
                    ]));
                    return response()->json($alarm, 201);
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jou organisatie',
        ], 403);
    }

    public function update(Request $request, Alarm $alarm)
    {
        $validator = Validator::make($request->all(), [
            'graph_type_id' => 'required|integer',
            'switch_value' => 'required|numeric',
            'operator' => 'required|string',
            'is_relais' => 'required|boolean',
            'is_notification' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(auth()->user()->is_superadmin){
            $alarm->update(array_merge(
                $request->all(),[
                'is_email_send' => 0
            ]));
            return response()->json($alarm,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $alarm->weather_station_id){
                    $alarm->update(array_merge(
                        $request->all(),[
                        'is_email_send' => 0
                    ]));
                    return response()->json($alarm,200);
                }
            }
        }
        return response()->json([
            'message' => 'Dit alarm is niet van jouw organisatie',
        ], 403);
    }


    public function destroy(Alarm $alarm)
    {
        if(auth()->user()->is_superadmin){
            $alarm->delete();
            return response()->json(null, 204);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $alarm->weather_station_id){
                    $alarm->delete();
                    return response()->json(null, 204);
                }
            }
        }
        return response()->json([
            'message' => 'Dit alarm is niet van jouw organisatie',
        ], 403);

    }

}
