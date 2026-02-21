<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\User;

$out = "TICKETS:\n";
$tickets = Ticket::latest()->take(10)->get();
foreach ($tickets as $t) {
    $out .= "ID: {$t->id}, TicketID: {$t->ticket_id}, Region: " . ($t->region_id ?? 'NULL') . ", Assigned: " . ($t->assigned_to ?? 'NULL') . "\n";
}

$out .= "\nIT STAFF:\n";
$it = User::whereIn('role', ['tim it', 'it'])->get();
foreach($it as $u) {
    $out .= "ID: {$u->id}, Name: {$u->name}, Region: " . ($u->region_id ?? 'NULL') . "\n";
}

file_put_contents('debug_final.txt', $out);
echo "Done\n";
