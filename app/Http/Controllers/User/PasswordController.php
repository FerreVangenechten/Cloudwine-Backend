<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller as Controller;

use App\Mail\ForgotMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordController extends Controller
{

    // Update and encrypt user password
    public function update(Request $request)
    {
        // Validate $request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Update encrypted user password in the database
        $user = User::findOrFail(auth()->id());

        if($validator->validated()){
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => "Your current password doesn't match the password in the database",
                ], 400);
            } else {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'message' => 'Password successfully updated',
                ], 201);
            }
        }

        return response()->json([
            'message' => 'Password successfully updated',
        ], 201);
    }

    // Update and encrypt user password
    public function reset(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user){
            $random = Str::random(20);
            $user->password = bcrypt($random);

            //MAIL
            $details = [
                'title' => 'Herstel Wachtwoord',
                'body' => 'Log je opnieuw in met het nieuwe wachtwoord en pas je wachtwoord opnieuw aan',
                'password' => $random
            ];

            \Mail::to($user->email)->send(new ForgotMail($details));
            $user->save();

            return response()->json([
                'message' => 'Check je mailbox voor emails, vergeet zeker je SPAM niet!',
            ], 201);
        }

        return response()->json([
            'message' => 'Email does not exist in our system, create an account',
        ], 400);

    }

}
