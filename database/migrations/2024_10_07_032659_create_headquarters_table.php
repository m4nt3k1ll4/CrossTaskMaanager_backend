<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadquartersTable extends Migration
{
    public function up()
    {
        Schema::create('headquarters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('manager_id')->nullable(); // Definir columna sin clave foránea
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('headquarters');
    }
}
