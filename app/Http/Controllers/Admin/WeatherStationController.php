<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeatherStation;
use Illuminate\Http\Request;

class WeatherStationController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation_id;
        $status = $request->get('active');

        if($status){
            if($status == 2){
                $weatherstations = WeatherStation::where('organisation_id', $organisation)->where('is_active',0)->with(['alarms','organisation'])->get();
            } else if($status == 1){
                $weatherstations = WeatherStation::where('organisation_id', $organisation)->with(['alarms','organisation'])->get();
            } else {
                $weatherstations = WeatherStation::where('organisation_id', $organisation)->where('is_active',1)->with(['alarms','organisation'])->get();
            }
            return response()->json($weatherstations,200);
        }

        return WeatherStation::where('organisation_id', $organisation)->where('is_active',1)->with(['alarms','organisation'])->get();
    }

    public function show(WeatherStation $weatherStation)
    {
        if(auth()->user()->is_superadmin){
            return $weatherStation;
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weatherStation->id){
                    return $weatherStation;
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 401);

    }

    public function update(Request $request, WeatherStation $weatherStation)
    {

        if(auth()->user()->is_superadmin){
            $weatherStation->update($request->all());
            return response()->json($weatherStation,200);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id == $weatherStation->id){
                    $weatherStation->update($request->all());
                    return response()->json($weatherStation,200);
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 401);

         //200 --> OK, The standard success code and default option
    }
}
