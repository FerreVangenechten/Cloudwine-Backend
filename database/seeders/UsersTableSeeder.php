<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // clear the users table first
//        User::truncate();

        $faker = \Faker\Factory::create();

        // Let's make sure everyone has the same password and
        // let's hash it before the loop, or else our seeder
        // will be too slow.
        $password = Hash::make('admin123');

        //SUPERADMIN
        User::create([
            'organisation_id' => null,
            'first_name' => 'Super',
            'surname' => 'Admin',
            'email' => 'super@super.com',
            'gsm' => '0470541285',
            'password' => $password,
            'is_active' => true,
            'is_admin' => true,
            'is_superadmin' => true,
            'can_message' => true,
            'can_receive_notification' => true,
        ]);

        //ADMIN ORGANISATIE 1
        User::create([
            'organisation_id' => 1,
            'first_name' => 'Administrator',
            'surname' => 'rotart',
            'email' => 'admin@admin.com',
            'gsm' => '0425896547',
            'password' => $password,
            'is_active' => true,
            'is_admin' => true,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);

        //standaard user
        User::create([
            'organisation_id' => 1,
            'first_name' => 'Ferre',
            'surname' => 'Vangenechten',
            'email' => 'ferrevangenechten@hotmail.com',
            'gsm' => '0470586561',
            'password' => $password,
            'is_active' => true,
            'is_admin' => false,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);

        //standaard user
        User::create([
            'organisation_id' => 1,
            'first_name' => 'Luuk',
            'surname' => 'Hoog',
            'email' => 'r0658495@student.thomasmore.be',
            'gsm' => '0485336655',
            'password' => $password,
            'is_active' => true,
            'is_admin' => false,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);

        User::create([
            'organisation_id' => 1,
            'first_name' => 'Klaas',
            'surname' => 'Eelen',
            'email' => 'r0740529@student.thomasmore.be',
            'gsm' => '0470586461',
            'password' => $password,
            'is_active' => true,
            'is_admin' => false,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);

        User::create([
            'organisation_id' => 1,
            'first_name' => 'Jens',
            'surname' => 'Fillée',
            'email' => 'r0800028@student.thomasmore.be',
            'gsm' => '0471558899',
            'password' => $password,
            'is_active' => true,
            'is_admin' => false,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);


        //standaard user
        User::create([
            'organisation_id' => 1,
            'first_name' => 'user',
            'surname' => 'resu',
            'email' => 'user@user.com',
            'gsm' => '0470522233',
            'password' => $password,
            'is_active' => true,
            'is_admin' => false,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,
        ]);

        // ORGANISATIE 1
        for ($i = 0; $i < 3; $i++) {
            User::create([
                'organisation_id' => 1,
                'first_name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->email,
                'gsm' => $faker->numerify('04########'),
                'password' => $password,
                'is_active' => true,
                'is_admin' => false,
                'is_superadmin' => false,
                'can_message' => false,
                'can_receive_notification' => true,

            ]);
        }

        //ADMIN ORGANISATIE 2
        User::create([
            'organisation_id' => 2,
            'first_name' => 'Administrator',
            'surname' => 'rotar2',
            'email' => 'admin2@admin2.com',
            'gsm' => '0422222222',
            'password' => $password,
            'is_active' => true,
            'is_admin' => true,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => false,

        ]);

        //ORGANISATIE 2
        for ($i = 0; $i < 3; $i++) {
            User::create([
                'organisation_id' => 2,
                'first_name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->email,
                'gsm' => $faker->numerify('04########'),
                'password' => $password,
                'is_active' => true,
                'is_admin' => false,
                'is_superadmin' => false,
                'can_message' => false,
                'can_receive_notification' => false,

            ]);
        }

        // ADMIN ORGANISATIE 3
        User::create([
            'organisation_id' => 3,
            'first_name' => 'Administrator',
            'surname' => 'rotar3',
            'email' => 'admin3@admin3.com',
            'gsm' => '0433333333',
            'password' => $password,
            'is_active' => true,
            'is_admin' => true,
            'is_superadmin' => false,
            'can_message' => true,
            'can_receive_notification' => true,

        ]);

        //ORGANISATIE 3
        for ($i = 0; $i < 8; $i++) {
            User::create([
                'organisation_id' => 3,
                'first_name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => $faker->email,
                'gsm' => $faker->numerify('04########'),
                'password' => $password,
                'is_active' => true,
                'is_admin' => false,
                'is_superadmin' => false,
                'can_message' => false,
                'can_receive_notification' => false,
            ]);
        }
    }
}
