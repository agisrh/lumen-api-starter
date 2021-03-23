<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchandiser_id');
            $table->string('system_version', 20)->nullable();
            $table->integer('sdk')->nullable();
            $table->string('manufacturer', 50)->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('codename', 50)->nullable();
            $table->string('app_version', 50)->nullable();
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
        Schema::dropIfExists('log_devices');
    }
}
