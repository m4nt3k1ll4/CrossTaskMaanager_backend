<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUsersAndHeadquarters extends Migration
{
    public function up()
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->foreign('headquarter_id')
                ->references('id')
                ->on('headquarters')
                ->onDelete('set null');
        });

        Schema::table('headquarters', function (Blueprint $table) {
            $table->foreign('manager_id')
                ->references('id')
                ->on('app_users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropForeign(['headquarter_id']);
        });

        Schema::table('headquarters', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });
    }
}

