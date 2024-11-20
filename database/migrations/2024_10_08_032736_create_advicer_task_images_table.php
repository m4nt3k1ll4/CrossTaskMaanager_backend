<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvicerTaskImagesTable extends Migration
{
    public function up()
    {
        Schema::create('advicer_task_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('app_users')->onDelete('cascade');
            $table->string('status'); 
            $table->string('image_path')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('advicer_task_images');
    }
}
