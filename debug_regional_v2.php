<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\User;

echo "LATEST 5 TICKETS:\n";
$tickets = Ticket::latest()->take(5)->get();
foreach ($tickets as $t) {
    echo "ID: {$t->id}, TicketID: {$t->ticket_id}, RegionID: " . ($t->region_id ?? 'NULL') . ", AssignedTo: " . ($t->assigned_to ?? 'NULL') . ", Status: {$t->status}\n";
}

echo "\nIT USERS IN REGION 1:\n";
$it1 = User::where('region_id', 1)->whereIn('role', ['tim it', 'it'])->get();
foreach($it1 as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, RegionID: {$u->region_id}\n";
}
