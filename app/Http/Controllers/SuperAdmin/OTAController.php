<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller as Controller;

use App\Models\OTA_Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OTAController extends Controller
{
    public function index()
    {
        return OTA_Update::all();
    }

    public function store(Request $request)
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

                return response()->json($save,201);
            }
        }
        return response()->json([
            'message' => 'Geen file geupload',
        ], 401);
    }

    public function show(OTA_Update $update)
    {
        return $update;
    }

    public function update(Request $request, OTA_Update $update)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|between:2,100|nullable',
            'deploy_on' => 'required|date_format:d-m-Y H:i:s',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($validator->validated()){
            $update->update($validator->validated());
        }
        return response()->json($update);

    }

    public function destroy(OTA_Update $update)
    {
        if(Storage::exists($update->bin_file_path)){
            Storage::delete($update->bin_file_path);
        }

        $update->delete();
        return response()->json(null, 204); //204 --> No content. When action was executed succesfully, but there is no content to return
    }
}
