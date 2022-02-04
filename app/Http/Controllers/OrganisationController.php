<?php

namespace App\Http\Controllers;

use Faker\Guesser\Name;
use Illuminate\Http\Request;
use App\Models\Organisation;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{

    public function show()
    {
        $organisation = Organisation::where('id',auth()->user()->organisation_id)->get();
        return response()->json($organisation[0]);
    }

    public function update(Request $request)
    {
        $organisation = Organisation::where('id', auth()->user()->organisation_id)->first();

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

        $organisation->update($validator->validated());
        return response()->json($organisation,200); //200 --> OK, The standard success code and default option
    }
}
