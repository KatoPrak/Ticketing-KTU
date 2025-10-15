<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->timestamps();
    });

    // Tambahkan kolom foreign key di tabel users
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('department_id')->nullable()->after('email');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        $table->dropColumn('department'); // hapus kolom lama
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
