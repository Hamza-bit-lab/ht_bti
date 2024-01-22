<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('position')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('degree')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('salary')->nullable();
            $table->integer('is_interviewer')->default(0);
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
        Schema::dropIfExists('employees');
    }
}
