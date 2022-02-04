<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    public function index()
    {
        $status = request()->active;

        if($status == 2){
            $organisation = Organisation::where('is_active', 0)->get();
        } else if($status == 1){
            $organisation = Organisation::all();
        } else {
            $organisation = Organisation::where('is_active', 1)->get();
        }
        return response()->json($organisation,200);
    }

    public function show(Organisation $organisation){return $organisation;}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'address' => 'required|string|between:2,100',
            'postal_code' => 'required|string|max:6',
            'city' => 'required|string',
            'country' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $organisation = Organisation::create(array_merge(
            $validator->validated(),
            [
                'is_active' => 1,
            ]
        ));

        return response()->json($organisation, 201);

    }

    public function update(Request $request, Organisation $organisation)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'address' => 'required|string|between:2,100',
            'postal_code' => 'required|string|max:6',
            'city' => 'required|string',
            'country' => 'required',
            'is_active' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $organisation->update($validator->validated());
        return response()->json($organisation,200); //200 --> OK, The standard success code and default option
    }

}
