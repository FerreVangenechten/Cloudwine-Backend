<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ActivationMail;
use App\Models\Organisation;
use App\Models\User;
use App\Models\WeatherStation;
use App\Models\WeatherStationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation_id;
        $status = $request->get('active');


        if($organisation && $status){
            if($status == 2){
                $users = User::where('organisation_id', $organisation)->where('is_active',0)->get();
            } else if($status == 1){
                $users = User::where('organisation_id', $organisation)->get();
            } else {
                $users = User::where('organisation_id', $organisation)->where('is_active',1)->get();
            }
            return response()->json($users,200);
        }

        $users = User::where('organisation_id',$organisation)->where('is_active',1)->get();
        return response()->json($users,200);
    }

    //register a user
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'surname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'is_admin' => 'required',
            'can_message' => 'required',
            'can_receive_notification' => 'required',
            'gsm' => 'string|unique:users,gsm|regex:/(04)[0-9]{8}/'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $random = Str::random(20);

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($random),
                'is_active' => 1,
                'organisation_id' => auth()->user()->organisation_id,
                'is_superadmin' =>0
            ]
        ));

        $organisatie = Organisation::where('id',auth()->user()->organisation_id)->first();
        //MAIL
        $details = [
            'title' => 'Welkom bij Wijnbouwer.be',
            'organisation' => $organisatie->name,
            'body' => ' Heeft u toegevoegd bij de organisatie op wijnbouwer.be, je kan jezelf inloggen met het onderstaande wachtwoord. Vergeet zeker je wachtwoord niet te veranderen',
            'password' => $random
        ];

        \Mail::to($request->email)->send(new ActivationMail($details));
        //ENDMAIL

        //create stationusers when user is added
        $weatherstations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get();
        foreach ($weatherstations as $weatherstation){
            WeatherStationUser::create([
                'weather_station_id' => $weatherstation->id,
                'user_id' => $user->id,
                'timeframe_temp' => null,
                'timerframe_hum' => null,
                'timeframe_lux' => null
            ]);
        }
        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'surname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
            'is_active' => 'required',
            'is_admin' => 'required',
            'can_message' => 'required',
            'can_receive_notification' => 'required',
            'gsm' => 'string|regex:/(04)[0-9]{8}/|unique:users,gsm,' . $user->id
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(auth()->user()->organisation_id == $user->organisation_id){
            $user->update($validator->validated());
            return response()->json($user,200);
        }else {
            return response()->json([
                'message' => 'Deze gebruiker zit niet bij jouw organisatie',
            ], 403);
        }
    }

    public function show(User $user)
    {
        if(auth()->user()->is_superadmin){
            return $user;
        } else if(auth()->user()->organisation_id == $user->organisation_id){
            return $user;
        }else {
            return response()->json([
                'message' => 'Deze gebruiker zit niet bij jouw organisatie',
            ], 403);
        }
    }
}
