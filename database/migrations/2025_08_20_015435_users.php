<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id (PK)
            $table->string('name', 100); // Nama user
            $table->string('email', 100)->unique(); // Email login
            $table->string('password'); // Password hash
            $table->enum('role', ['user', 'it', 'admin'])->default('user'); // Role user
            $table->string('department', 100)->nullable(); // Departemen user
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
