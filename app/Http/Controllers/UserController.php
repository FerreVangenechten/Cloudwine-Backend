<?php

namespace App\Http\Controllers;

use App\Models\WeatherStation;
use App\Models\WeatherStationUser;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $organisation = $request->get('organisation');
        $order = $request->get('order');
        $status = $request->get('active');

        $users = User::where('organisation_id', $organisation)->get();

        if($organisation){
            if($status == 2){
                $users = User::where('organisation_id', $organisation)->get();
            }
            else if($status == '0'){
                $users = User::where('organisation_id', $organisation)->where('is_active',0)->get();
            }
            else {
                $users = User::where('organisation_id', $organisation)->where('is_active',1)->get();
            }
            return response()->json($users,200);
        }

        if($order){
            if($status == 2){
                $users = User::orderBy('first_name', $order)->get();
            }
            else if($status == '0'){
                $users = User::orderBy('first_name', $order)->where('is_active',0)->get();
            }
            else {
                $users = User::orderBy('first_name', $order)->where('is_active',1)->get();
            }
            return response()->json($users,200);
        }

        $users = User::orderBy('organisation_id','asc')->where('is_active',1)->get();
        return response()->json($users,200);
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        $weatherstations = WeatherStation::where('organisation_id',$request->get('organisation_id'))->get();
        foreach ($weatherstations as $weatherstation){
            WeatherStationUser::create([
                'weather_station_id' => $weatherstation->id,
                'user_id' => $user->id,
                'timeframe_temp' => null,
                'timerframe_hum' => null,
                'timeframe_lux' => null
            ]);
        }
        return response()->json($user, 201); //201 --> Object created. Usefull for the store actions
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return response()->json($user,200); //200 --> OK, The standard success code and default option
    }

    public function delete(User $user)
    {
        $user->delete();
        return response()->json(null, 204); //204 --> No content. When action was executed succesfully, but there is no content to return
    }
}
