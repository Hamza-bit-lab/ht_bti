<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('name')->nullable();
            $table->text('email')->nullable();
            $table->text('phone')->nullable();
            $table->text('c_salary')->nullable();
            $table->text('e_salary')->nullable();
            $table->text('notice_period')->nullable();
            $table->timestamp('interview')->nullable();
            $table->text('document')->nullable();
            $table->text('comment')->nullable();
            $table->text('type')->nullable();
            $table->text('meeting_url')->nullable();
            $table->text('status')->default('Pending');
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
        Schema::dropIfExists('interviews');
    }
}
