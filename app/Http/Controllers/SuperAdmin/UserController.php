<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller as Controller;
use App\Mail\ActivationMail;
use App\Models\WeatherStation;
use App\Models\WeatherStationUser;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $organisation = $request->get('organisation');
        $status = $request->get('active');

        if($organisation){
            if($status == 2){
                $users = User::where('organisation_id', $organisation)->where('is_active',0)->with('organisation')->get();
            } else if($status == 1){
                $users = User::where('organisation_id', $organisation)->with('organisation')->get();
            } else {
                $users = User::where('organisation_id', $organisation)->where('is_active',1)->with('organisation')->get();
            }
            return response()->json($users,200);
        }else if($status){
            if($status == 2){
                $users = User::orderBy('organisation_id','asc')->where('is_active',0)->with('organisation')->get();
            } else if($status == 1){
                $users = User::orderBy('organisation_id','asc')->with('organisation')->get();
            } else {
                $users = User::orderBy('organisation_id','asc')->where('is_active',1)->with('organisation')->get();            }
            return response()->json($users,200);
        }
        $users = User::where('is_active',1)->with('organisation')->get();
        return response()->json($users,200);
    }

    public function show($id)
    {
        return User::find($id);
    }

    //register a user
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'organisation_id' =>'int|nullable',
            'first_name' => 'required|string|between:2,100',
            'surname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'is_active' => 'required',
            'is_admin' => 'required',
            'is_superadmin' => 'required',
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
            ['password' => bcrypt($random)]
        ));

        //MAIL
        $details = [
            'title' => 'Welkom bij Wijnbouwer.be',
            'organisation' => '',
            'body' => 'U bent toegewezen als administrator op wijnbouwer.be, je kan jezelf inloggen met het onderstaande wachtwoord. Vergeet zeker je wachtwoord niet te veranderen',
            'password' => $random
        ];

        \Mail::to($request->email)->send(new ActivationMail($details));

        //ENDMAIL

        //create stationusers when user is added
        if($request->organisation_id !== null){
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
        }

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'organisation_id' =>'int|nullable',
            'first_name' => 'required|string|between:2,100',
            'surname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $user->id ,
            'is_active' => 'required',
            'is_admin' => 'required',
            'is_superadmin' => 'required',
            'can_message' => 'required',
            'can_receive_notification' => 'required',
            'gsm' => 'string|regex:/(04)[0-9]{8}/|unique:users,gsm,' . $user->id
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        //delete existing weatherstationusers and add new if organisatioin is changed
        if($request->organisation_id !== null && $request->organisation_id !== $user->organisation_id){
            $stationUsers = WeatherStationUser::where('user_id',$user->id)->get();
            foreach ($stationUsers as $stationUser){
                $stationUser->delete();
            }
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
            //delete all stationusers when no organisation
        } else if($request->organisation_id == null){
            $stationUsers = WeatherStationUser::where('user_id',$user->id)->get();
            foreach ($stationUsers as $stationUser){
                $stationUser->delete();
            }
        }

        //update the user
        if($validator->validated()){
            $user->update($request->all());
        }
        return response()->json($user,200); //200 --> OK, The standard success code and default option
    }
}
