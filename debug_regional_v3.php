<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ticket;
use App\Models\User;

$tickets = Ticket::latest()->take(5)->get();
echo "TICKETS:\n";
foreach ($tickets as $t) {
    echo "ID: {$t->id}, Region: {$t->region_id}, Assigned: {$t->assigned_to}\n";
}

echo "\nIT STAFF:\n";
$it = User::whereIn('role', ['tim it', 'it'])->get();
foreach($it as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Region: {$u->region_id}\n";
}
