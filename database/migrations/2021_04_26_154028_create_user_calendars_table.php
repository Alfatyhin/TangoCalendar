<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_calendars', function (Blueprint $table) {
            $table->id();
            $table->integer('userId')->index();
            $table->string('calendarId')->index()->unique();
            $table->string('type_events')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('source')->nullable();
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
        Schema::dropIfExists('user_calendars');
    }
}
