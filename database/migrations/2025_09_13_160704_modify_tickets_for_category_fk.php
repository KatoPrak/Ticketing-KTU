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
    Schema::table('tickets', function (Blueprint $table) {
        // Hapus kolom 'category' yang lama (bertipe varchar)
        $table->dropColumn('category');
        
        // Tambahkan kolom 'category_id' sebagai foreign key
        $table->foreignId('category_id')
              ->after('user_id') // Posisikan setelah kolom user_id (opsional)
              ->constrained('categories') // Menghubungkan ke tabel 'categories'
              ->onDelete('cascade'); // Jika kategori dihapus, tiket terkait juga terhapus
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
        });
    }
};
