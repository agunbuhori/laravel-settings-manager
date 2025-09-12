<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bag')->nullable()->index();
            $table->string('group')->nullable()->index();
            $table->string('key')->index();
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'array']);
            $table->text('value')->nullable();
            $table->boolean('cache')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
