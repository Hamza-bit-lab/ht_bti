<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $hash=Hash::make('12345678');
        DB::table('users')->insert([

            ['name' => 'HR','email'=>'hr@gmail.com','role'=>'hr','password'=>''.$hash.''],

        ]);
        // $employees = Employee::all();

        // foreach ($employees as $employee) {
        //     $email = $employee->email;
        //     $name = $employee->name;

        //     $password = explode('@', $email)[0];

        //     // Insert data into the users table
        //     DB::table('users')->insert([
        //         'name' => $name,
        //         'email' => $email,
        //         'password' => Hash::make($password),
        //         'role' => 'employee',
        //     ]);
        // }
    }
}
