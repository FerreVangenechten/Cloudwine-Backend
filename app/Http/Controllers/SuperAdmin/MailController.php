<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller as Controller;

use App\Models\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{
    public function index(){return Mail::all();}

    public function show(Mail $mail){return $mail;}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' =>'required|string',
            'subject' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            $mail = Mail::create($request->all());
        }

        return response()->json($mail, 201); //201 --> Object created. Usefull for the store actions
    }

    public function update(Request $request, Mail $mail)
    {
        $validator = Validator::make($request->all(), [
            'message' =>'required|string',
            'subject' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            $mail->update($request->all());
        }

        return response()->json($mail,200); //200 --> OK, The standard success code and default option
    }

    public function destroy(Mail $mail)
    {
        $mail->delete();
        return response()->json(204);
    }
}
