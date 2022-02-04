<?php

namespace Database\Seeders;

use App\Models\Value;
use Illuminate\Database\Seeder;

class ValueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $faker = \Faker\Factory::create();


        //ADD DATA WHEATERSTATION 1
        for ($a = 0; $a < 80; $a++) {
            $date = $faker->dateTimeBetween('-10 day', '+ 1 day');
            for ($i = 1; $i < 21; $i++) {
                if($i == 20){
                    Value::create([
                        'weather_station_id' => 1,
                        'graph_type_id' => $i,
                        'value' => 0,
                        'timestamp' => $date,
                    ]);
                } else {
                    Value::create([
                        'weather_station_id' => 1,
                        'graph_type_id' => $i,
                        'value' => strval($faker->randomDigit),
                        'timestamp' => $date,
                    ]);
                }
            }
        }

        //ADD DATA WHEATERSTATION 2
        for ($a = 0; $a < 30; $a++) {
            $date = $faker->dateTimeBetween('-5 day', '+ 1 day');
            for ($i = 1; $i < 21; $i++) {
                if($i == 20){
                    Value::create([
                        'weather_station_id' => 2,
                        'graph_type_id' => $i,
                        'value' => 0,
                        'timestamp' => $date,
                    ]);
                } else {
                    Value::create([
                        'weather_station_id' => 2,
                        'graph_type_id' => $i,
                        'value' => strval($faker->randomDigit),
                        'timestamp' => $date,
                    ]);
                }
            }
        }

        //ADD DATA WHEATERSTATION 3
        for ($a = 0; $a < 4; $a++) {
            $date = $faker->dateTimeBetween('-1 day', '+ 1 day');
            for ($i = 1; $i < 21; $i++) {
                if($i == 20){
                    Value::create([
                        'weather_station_id' => 3,
                        'graph_type_id' => $i,
                        'value' => 0,
                        'timestamp' => $date,
                    ]);
                } else {
                    Value::create([
                        'weather_station_id' => 3,
                        'graph_type_id' => $i,
                        'value' => strval($faker->randomDigit),
                        'timestamp' => $date,
                    ]);
                }
            }
        }

        //ADD DATA WHEATERSTATION 4
        for ($a = 0; $a < 4; $a++) {
            $date = $faker->dateTimeBetween('-1 day', '+ 1 day');
            for ($i = 1; $i < 21; $i++) {
                if($i == 20){
                    Value::create([
                        'weather_station_id' => 4,
                        'graph_type_id' => $i,
                        'value' => 0,
                        'timestamp' => $date,
                    ]);
                } else {
                    Value::create([
                        'weather_station_id' => 4,
                        'graph_type_id' => $i,
                        'value' => strval($faker->randomDigit),
                        'timestamp' => $date,
                    ]);
                }
            }
        }
    }
}
