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
        Schema::table('ticket_transfer_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_transfer_logs', 'note')) {
                $table->text('note')->nullable()->after('transferred_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_transfer_logs', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_transfer_logs', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
