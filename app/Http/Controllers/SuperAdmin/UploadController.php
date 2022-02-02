<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OTA_Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'name' => 'string|between:2,100|nullable',
            'deploy_on' => 'required|date_format:d-m-Y H:i:s',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($validator->validated()){
            if ($file = $request->file('file')) {
                $path = $file->store('public/updates');
                if($request->name){
                    $name = $request->name;
                } else {
                    $name = $file->getClientOriginalName();
                }

                //store your file into directory and db
                $save = new OTA_Update();
                $save->name = $name;
                $save->bin_file_path= $path;
                $save->deploy_on = $request->deploy_on;
                $save->save();

                return response()->json([
                    "success" => true,
                    "message" => "File successfully uploaded",
                    "file" => $save
                ]);
            }
            return response()->json([
                'message' => 'Geen file geupload',
            ], 401);
        }
    }

    public function download(Request $request)
    {
//        $validator = Validator::make($request->all(),[
//            'file' => 'required|mimes:binary|max:2048',
//        ]);
//
//        if($validator->fails()) {
//
//            return response()->json(['error'=>$validator->errors()], 401);
//        }


    }

}
