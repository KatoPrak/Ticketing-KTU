<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$it = User::whereIn('role', ['tim it', 'it'])->get();
foreach($it as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Role: '{$u->role}', Region: " . ($u->region_id ?? 'NULL') . "\n";
}
