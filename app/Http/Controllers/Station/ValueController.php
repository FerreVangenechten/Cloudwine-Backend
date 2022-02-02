<?php

namespace App\Http\Controllers\Station;

use App\Http\Controllers\Controller;
use App\Mail\AlarmMail;
use App\Models\Alarm;
use App\Models\GraphType;
use App\Models\User;
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

        foreach ($graphTypes as $type) {
            $name = $type->name;
            Value::create([
                'weather_station_id' => $weatherStation->id,
                'graph_type_id' => $type->id,
                'value' => $request->$name,
                'timestamp' => $request->time,
            ]);
        }

        //get all alarms from the weatherstation
        $alarms = Alarm::where('weather_station_id', $weatherStation->id)->where('is_notification',1)->with('graphType')->get();

        //get all users that can receive notification
        $mailableUsers = User::where('organisation_id',$weatherStation->organisation_id)->where('can_receive_notification',1)->get();

//        $test = [];
        foreach ($alarms as $alarm){
            $typeName = $alarm->graphType->name;
            if($alarm->operator == "<"){
                if($request->$typeName < $alarm->switch_value && $alarm->is_email_send == 0){
//                    array_push($test, $request->$typeName . ' is kleiner dan ' . $alarm->switch_value);

                    //MAIL
                    $details = [
                        'title' => 'Alarm weerstation: ',
                        'name' => $weatherStation->name,
                        'alarm' => 'Sensor ' . $typeName . ' is kleiner dan de alarmwaarde ' . $alarm->switch_value,
                        'body' => 'U ontvangt deze melding omdat de ' . $typeName . ' sensor momenteel ' . $request->$typeName .' is, dit kleiner dan de ingestelde alarmwaarde van ' . $alarm->switch_value,
                    ];

                    foreach ($mailableUsers as $user){
//                        array_push($test,$user->email);
                        \Mail::to($user->email)->send(new AlarmMail($details));
                    }
                    //ENDMAIL

                    //email send = true
                    $alarm->is_email_send = 1;
                    $alarm->save();
                } else if ($request->$typeName > $alarm->switch_value){
                    //email send = false
                    $alarm->is_email_send = 0;
                    $alarm->save();
                }
            } else {
                if($request->$typeName > $alarm->switch_value && $alarm->is_email_send == 0){
//                    array_push($test, $request->$typeName . ' is groter dan ' . $alarm->switch_value);

                    //MAIL
                    $details = [
                        'title' => 'Alarm weerstation: ' ,
                        'name' => $weatherStation->name,
                        'alarm' => 'Sensor ' . $typeName . ' is groter dan de alarmwaarde ' . $alarm->switch_value,
                        'body' => 'U ontvangt deze melding omdat de ' . $typeName . ' sensor momenteel ' . $request->$typeName .' is, dit groter dan de ingestelde alarmwaarde van ' . $alarm->switch_value,
                    ];

                    foreach ($mailableUsers as $user){
//                        array_push($test,$user->email);
                        \Mail::to($user->email)->send(new AlarmMail($details));
                    }
                    //ENDMAIL

                    $alarm->is_email_send = 1;
                    $alarm->save();
                } else if ($request->$typeName < $alarm->switch_value){
                    $alarm->is_email_send = 0;
                    $alarm->save();
                }
            }
        }

//        return response()->json([$test,$alarms],201); //201 --> Object created. Usefull for the store actions
        return response()->json('data is created',201); //201 --> Object created. Usefull for the store actions

    }
}
