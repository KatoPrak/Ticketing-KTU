<?php
// database/seeders/TicketSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan factory untuk membuat 150 tiket
        Ticket::factory()->count(1)->create();
    }
}