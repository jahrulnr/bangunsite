<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('ssh', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->integer('port');
            $table->string('user');
            $table->string('pass');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ssh');
    }
};
