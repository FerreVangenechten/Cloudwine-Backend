<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alarm;

class AlarmTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //This resets the table, deleting all the data everytime the function is called.

        $faker = \Faker\Factory::create();


        for ($i = 0; $i < 3; $i++) {
            Alarm::create([
                'weather_station_id' => 1,
                'graph_type_id' => $faker->numberBetween(1,12),
                'switch_value' => $faker->randomFloat(2, -20, 30),
                'operator' => '<',
                'is_relais' => true,
                'is_notification' => false,
                'is_email_send' => false,

            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Alarm::create([
                'weather_station_id' => 2,
                'graph_type_id' => $faker->numberBetween(1,12),
                'switch_value' => $faker->randomFloat(2, -20, 30),
                'operator' => '>',
                'is_relais' => true,
                'is_notification' => true,
                'is_email_send' => false,
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Alarm::create([
                'weather_station_id' => 3,
                'graph_type_id' => $faker->numberBetween(1,12),
                'switch_value' => $faker->randomFloat(2, -20, 30),
                'operator' => '<',
                'is_relais' => false,
                'is_notification' => false,
                'is_email_send' => false,
            ]);
        }

    }
}
