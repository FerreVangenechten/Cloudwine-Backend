<?php

namespace App\Http\Controllers\Station;

use App\Http\Controllers\Controller;
use App\Models\Alarm;
use App\Models\GraphType;
use App\Models\Value;
use App\Models\WeatherStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValueController extends Controller
{
    //ALLEEN VOOR WEERSTATION
    public function state($weather_station_gsm)
    {
        $weatherstation = WeatherStation::where('gsm',$weather_station_gsm)->first();
        $state = $weatherstation->switch_state;
        $manual = $weatherstation->is_manual_relais;
        return response()->json([
            'switch_state' => $state,
            'is_manual_relais' => $manual],200);
    }

    public function stateupdate(Request $request,$weather_station_gsm)
    {
        // Validate $request
        $validator = Validator::make($request->all(), [
            'switch_state' => 'required|boolean',
            'is_manual_relais' => 'required|boolean'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $weatherstation = WeatherStation::where('gsm',$weather_station_gsm)->first();

        //update the user
        if($validator->validated()){
            $weatherstation->update($request->all());
        }

        return response()->json([
            'message' => 'Updated state successfully',
        ], 200);

    }

    public function store(Request $request)
    {
        $graphTypes = GraphType::all();
        $weatherStation = WeatherStation::where('gsm',$request->gsm)->first();

//        foreach ($graphTypes as $type) {
//            $name = $type->name;
//            Value::create([
//                'weather_station_id' => $weatherStation->id,
//                'graph_type_id' => $type->id,
//                'value' => $request->$name,
//                'timestamp' => $request->time,
//            ]);
//        }


        //get all alarms from the weatherstation
        $alarms = Alarm::where('weather_station_id', $weatherStation->id)->where('is_notification',1)->with('graphType')->get();

        $test = [];
        foreach ($alarms as $alarm){
            $TypeName = $alarm->graphType->name;
            if($alarm->operator == "<"){
                if($request->$TypeName < $alarm->switch_value){
                    array_push($test, $request->$TypeName . ' is kleiner dan ' . $alarm->switch_value);
                }
            } else {
                if($request->$TypeName > $alarm->switch_value){
                    array_push($test, $request->$TypeName . ' is groter dan ' . $alarm->switch_value);
                }
            }
        }

        //send emails to all users from the station's organisation with notifications on

        return response()->json([$test,$alarms],201); //201 --> Object created. Usefull for the store actions
//        return response()->json('data is created',201); //201 --> Object created. Usefull for the store actions

    }
}
