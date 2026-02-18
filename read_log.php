<?php
$log = file_get_contents(__DIR__ . '/storage/logs/laravel.log');

$out = '';

// Find all "notifikasi" occurrences (last 5)
$positions = [];
$offset = 0;
while (($pos = strpos($log, 'notifikasi', $offset)) !== false) {
    $positions[] = $pos;
    $offset = $pos + 1;
}
$positions = array_slice($positions, -5); // last 5

$out .= "=== Last 5 'notifikasi' entries ===\n";
foreach ($positions as $i => $pos) {
    $start = max(0, $pos - 80);
    $end = min(strlen($log), $pos + 250);
    $out .= "--- Match " . ($i+1) . " ---\n";
    $out .= substr($log, $start, $end - $start) . "\n\n";
}

$out .= "\n=== Last 'gagal dikirim' ===\n";
$pos = strrpos($log, 'gagal dikirim');
if ($pos !== false) {
    $out .= substr($log, max(0, $pos - 150), 600) . "\n";
}

$out .= "\n=== Last '535' error ===\n";
$pos = strrpos($log, '535');
if ($pos !== false) {
    $out .= substr($log, max(0, $pos - 300), 700) . "\n";
}

file_put_contents(__DIR__ . '/log_output.txt', $out);
echo "Done. Written to log_output.txt\n";
