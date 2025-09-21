<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('email');
            $table->enum('role', ['user','tim it', 'admin',])->default('user')->after('department');
            $table->string('id_staff')->nullable()->unique()->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'role', 'id_staff',]);
        });
    }
};