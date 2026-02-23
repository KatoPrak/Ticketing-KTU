<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = \App\Models\User::where('username', 'admin')->first();
if ($u) {
    $u->password = \Illuminate\Support\Facades\Hash::make('password123');
    $u->save();
    echo "Password for username 'admin' has been reset to 'password123'\n";
} else {
    echo "User 'admin' not found\n";
}

$u = \App\Models\User::where('username', 'it.team')->first();
if ($u) {
    $u->password = \Illuminate\Support\Facades\Hash::make('password123');
    $u->save();
    echo "Password for username 'it.team' has been reset to 'password123'\n";
}
