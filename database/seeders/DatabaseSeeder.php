<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\TicketFactory;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder lain secara berurutan
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TicketSeeder::class, // Factory akan dijalankan di sini
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@company.com / admin123');
        $this->command->info('User credentials: Any user email / password123');
    }
}