<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\User;

$tickets = Ticket::latest()->take(5)->get(['id', 'ticket_id', 'user_id', 'region_id', 'assigned_to', 'status']);
$it_users = User::whereIn('role', ['it', 'tim it'])->get(['id', 'name', 'role', 'region_id']);

echo "TICKETS:\n";
foreach($tickets as $t) {
    echo "ID: {$t->id}, CID: {$t->ticket_id}, UserID: {$t->user_id}, RegionID: " . ($t->region_id ?? 'NULL') . ", AssignedTo: " . ($t->assigned_to ?? 'NULL') . ", Status: {$t->status}\n";
}

echo "\nIT USERS:\n";
foreach($it_users as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Role: {$u->role}, RegionID: " . ($u->region_id ?? 'NULL') . "\n";
}
