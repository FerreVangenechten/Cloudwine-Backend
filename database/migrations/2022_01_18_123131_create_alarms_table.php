<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_station_id');
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->boolean('is_relais')->default(false);
            $table->boolean('is_notification')->default(false);
            $table->timestamps();

            // Foreign key relation
            $table->foreign('weather_station_id')->references('id')->on('weather_stations')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alarms');
    }
}
