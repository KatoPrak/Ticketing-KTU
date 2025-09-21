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
        Schema::table('users', function (Blueprint $table) {
            // Add remember_token column if it doesn't exist
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('password');
            }
            
            // Add last_login_at column (optional)
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
            
            // Change employee_id to id_staff if it exists
            if (Schema::hasColumn('users', 'employee_id') && !Schema::hasColumn('users', 'id_staff')) {
                $table->renameColumn('employee_id', 'id_staff');
            } elseif (!Schema::hasColumn('users', 'id_staff')) {
                $table->string('id_staff')->unique()->after('name');
            }
            
            // Add indexes for performance
            if (!$this->hasIndex('users', 'users_remember_token_index')) {
                $table->index('remember_token');
            }
            
            if (!$this->hasIndex('users', 'users_id_staff_index')) {
                $table->index('id_staff');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['remember_token', 'last_login_at']);
            $table->dropIndex(['remember_token']);
            $table->dropIndex(['id_staff']);
        });
    }

    /**
     * Check if index exists
     */
    private function hasIndex($table, $indexName)
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes($table);
        return array_key_exists($indexName, $indexes);
    }
};