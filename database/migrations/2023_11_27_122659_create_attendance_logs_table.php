<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->foreign('attendance_id')->references('attendance_id')->on('employees');
            $table->string('name');
            $table->date('date');
            $table->time('checkIn')->nullable();
            $table->time('checkOut')->nullable();
            $table->integer('adjustmentValue')->nullable();
            $table->string('adjustmentType')->nullable();
            $table->time('totalTime')->nullable();
            $table->time('requiredTime')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_logs');
    }
}
