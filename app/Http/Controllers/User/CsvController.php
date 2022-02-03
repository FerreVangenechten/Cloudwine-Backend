<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GraphType;
use App\Models\Value;
use App\Models\WeatherStation;
use Carbon\Carbon;
use Faker\Core\File;
use Illuminate\Http\Request;

class CsvController extends Controller
{
    public function get_csv(Request $request,$weather_station_id){

        $from = date($request->get('start'));
        $to = date($request->get('stop'));

        if(!$from){
            $from = Carbon::now()->subDays(365)->toDateString();
        }
        if(!$to){
            //add one day for the 'inbetween' function
            $to = Carbon::now()->addDays(1)->toDateString();
        }

        $timestampValues = Value::where('weather_station_id',$weather_station_id)
            ->with('graphType')
            ->orderBy('timestamp')
            ->whereBetween('timestamp',[$from,$to])
            ->get()
            ->groupBy('timestamp');

        // these are the headers for the csv file.
        $headers = array(
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename=download.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        );

        //I am storing the csv file in public >> files folder. So that why I am creating files folder
//        if (!\Illuminate\Support\Facades\File::exists(public_path()."/downloads")) {
//            \Illuminate\Support\Facades\File::makeDirectory(public_path() . "/downloads");
//        }

        //creating the download file
        $filename =  public_path("downloads/download.csv");
        $handle = fopen($filename, 'w');

        $graphTypes = GraphType::all();
        $names = [];
        foreach($graphTypes as $type){
            array_push($names,$type->name);
        }
        array_push($names,'timestamp');


        //adding the first row with the HEADERNAMES (T1,T2,T3)
        fputcsv($handle, $names);

        //loop over all timestamp groups
        foreach ($timestampValues as $values){
            $valuesPerTimeframe = [];
            //loop over all values and put the values in the array
            foreach ($values as $value){
                array_push($valuesPerTimeframe,$value->value);
            }
            array_push($valuesPerTimeframe,$values[0]->timestamp);
            //add the data from the array to the csv
            fputcsv($handle, $valuesPerTimeframe);
        }
        //close the file
        fclose($handle);

//        return response()->json($timestampValues);
//        return \Response::download($filename, "download.csv", $headers);


        if(auth()->user()->is_superadmin){
            return \Response::download($filename, "download.csv", $headers);
        }else {
            $userStations = WeatherStation::where('organisation_id',auth()->user()->organisation_id)->get('id');
            foreach ($userStations as $station){
                if($station->id ==$weather_station_id){
                    return \Response::download($filename, "download.csv", $headers);
                }
            }
        }
        return response()->json([
            'message' => 'Dit weerstation zit niet bij jouw organisatie',
        ], 403);

        //download command
    }

}
