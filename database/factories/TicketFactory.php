<?php
// database/factories/TicketFactory.php
namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        // Nilai untuk status dan prioritas ini harus cocok dengan yang ada di ENUM database Anda
        $statuses = ['waiting', 'in_progress', 'pending', 'resolved']; 
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        $createdAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $status = $this->faker->randomElement($statuses);
        
        $resolvedAt = null;
        if ($status === 'resolved') {
            $resolvedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        }

        return [
            'user_id' => User::where('role', '!=', 'admin')->inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'description' => $this->faker->paragraph(rand(2, 5)),
            'priority' => $this->faker->randomElement($priorities),
            'status' => $status,
            'resolved_at' => $resolvedAt,
            'resolution_notes' => $status === 'resolved' ? 'Issue resolved successfully after investigation.' : null,
            'attachments' => null,
            'created_at' => $createdAt,
            'updated_at' => $resolvedAt ?? $createdAt,
            
            // DIHAPUS: Kolom-kolom ini tidak ada di tabel tickets Anda
            // 'assigned_to' => $status !== 'pending' ? User::where('role', 'admin')->first()->id : null,
            // 'system' => $this->faker->randomElement(['Windows 11 Pro', 'macOS Sonoma', 'Ubuntu 22.04']),
            // 'location' => $this->faker->randomElement(['Building A, Floor 1', 'Building B, Floor 2', 'Remote Office']),
            // 'asset_id' => 'AST' . $this->faker->unique()->numberBetween(100, 999),
        ];
    }
}