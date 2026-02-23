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
            Regional1UserSeeder::class,
            Regional2UserSeeder::class,
            Regional3UserSeeder::class,
            TicketSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@company.com / admin123');
        $this->command->info('User credentials: Any user email / password123');
    }
}