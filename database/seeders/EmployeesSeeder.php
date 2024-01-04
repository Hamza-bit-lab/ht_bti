<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            $joiningDate = $faker->dateTimeBetween('-5 years', 'now');

            $phoneNumber = '03' . $faker->numberBetween(00, 99) . $faker->randomNumber(7);

            $salary = $faker->numberBetween(100, 400) * 500;

            Employee::create([
                'name' => $faker->name,
                'position' => $faker->jobTitle,
                'email' => $faker->unique()->safeEmail,
                'phone' => $phoneNumber,
                'degree' => $faker->word,
                'joining_date' => $joiningDate,
                'salary' => $salary,
                'is_interviewer' => $faker->randomElement([0, 1]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
