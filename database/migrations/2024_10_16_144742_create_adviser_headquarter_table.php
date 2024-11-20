<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdviserHeadquarterTable extends Migration
{
    public function up()
    {
        Schema::create('adviser_headquarters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->foreignId('headquarter_id')->constrained('headquarters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adviser_headquarters');
    }
}


