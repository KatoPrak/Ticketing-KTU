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
        Schema::create('ticket_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('1-5 stars'); // 1-5
            $table->text('comment');
            $table->timestamps();
            
            // Index untuk performa
            $table->index('ticket_id');
            $table->index('user_id');
            
            // Unique constraint: satu ticket hanya bisa punya satu feedback
            $table->unique('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_feedbacks');
    }
};