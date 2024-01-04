<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interview;
use App\Models\Employee;
use Carbon\Carbon;
use App\Interfaces\InterviewStatus;
use Faker\Factory as Faker;

class InterviewsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_US');
        $interviewerIds = Employee::where('is_interviewer', 1)->pluck('id');
        $statuses = InterviewStatus::STATUSES;

        for ($i = 0; $i < 1000; $i++) {
            $type = $i % 2 === 0 ? 'online' : 'physical';
            $meetingUrl = $type === 'online' ? 'https://example.com/meeting_' . $i : null;
            $status = $statuses[array_rand($statuses)];

            $randomDate = $faker->dateTimeBetween('-1 years', '+1 years');
            $randomDate->setTime($faker->numberBetween(10, 18), $faker->numberBetween(0, 59), $faker->numberBetween(0, 59));

            $cSalary = $faker->numberBetween(50000, 100000);
            $eSalary = $faker->numberBetween($cSalary + 25000, 150000);
            
            $cSalary -= $cSalary % 500;
            $eSalary -= $eSalary % 500;

            $phoneNumber = '03' . $faker->numberBetween(00, 99) . $faker->randomNumber(7);

            Interview::create([
                'title' => $faker->jobTitle,
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $phoneNumber,
                'c_salary' => $cSalary,
                'e_salary' => $eSalary,
                'notice_period' => $faker->numberBetween(1, 30),
                'date' => $randomDate,
                'document' => 'document_' . $faker->word . '_' . $i . '.pdf',
                'interviewer' => $interviewerIds->random(),
                'interviewer_comments' => '',
                'hr_comments' => $faker->realText(50),
                'type' => $type,
                'meeting_url' => $meetingUrl,
                'status' => $status,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}

