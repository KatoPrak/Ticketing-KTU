<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::create('news', function (Blueprint $table) {
        $table->id(); // Membuat kolom 'id' (bigint, unsigned, auto-increment, primary key)
        $table->text('message'); // Membuat kolom 'message' dengan tipe TEXT
        $table->timestamps(); // Membuat kolom 'created_at' dan 'updated_at' (timestamp, nullable)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
