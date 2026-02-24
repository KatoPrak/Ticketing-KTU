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
            LocationSeeder::class,
            RegionSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            Regional1UserSeeder::class,
            Regional2UserSeeder::class,
            Regional3UserSeeder::class,
            TicketSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: username: admin / password: password');
        $this->command->info('IT Team credentials: username: it.team / password: password');
    }
}