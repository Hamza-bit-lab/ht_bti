<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOfficeHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_hours', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('starting_time')->nullable();
            $table->time('break_starting')->nullable();
            $table->time('break_ending')->nullable();
            $table->time('closing_time')->nullable();
            $table->boolean('isON')->default(true);
            $table->timestamps();
        });
        $this->seedOfficeHours();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_hours');
    }

    protected function seedOfficeHours()
    {
        $days = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        ];

        foreach ($days as $day) {
            $startingTime = $day === 'Saturday' || $day === 'Sunday' ? '00:00:00' : '10:00:00';
            $closingTime = $day === 'Saturday' || $day === 'Sunday' ? '00:00:00' : '19:00:00';
            $breakStarting = $day === 'Saturday' || $day === 'Sunday' ? '00:00:00' : '13:30:00';
            $breakEnding = $day === 'Saturday' || $day === 'Sunday' ? '00:00:00' : '14:30:00';
            $isON = $day === 'Saturday' || $day === 'Sunday' ? 0 : 1;

            DB::table('office_hours')->insert([
                'day' => $day,
                'starting_time' => $startingTime,
                'closing_time' => $closingTime,
                'break_starting' => $breakStarting,
                'break_ending' => $breakEnding,
                'isON' => $isON,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
