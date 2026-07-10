<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Process;
use Illuminate\Process\Pool;
use App\Models\DataSwitch;

$switches = DataSwitch::select('id', 'ip_address')->get();
$isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

$poolResults = Process::pool(function (Pool $pool) use ($switches, $isWindows) {
    foreach ($switches as $switch) {
        $ip = trim($switch->ip_address ?? '');
        if (empty($ip)) {
            continue;
        }
        $command = $isWindows
            ? "ping -n 1 -w 1000 " . escapeshellarg($ip)
            : "ping -c 1 -W 1 " . escapeshellarg($ip);
        $pool->as("switch_{$switch->id}")->command($command);
    }
})->start()->wait();

$newStatuses = [];
foreach ($switches as $switch) {
    $id = "switch_{$switch->id}";
    $newStatuses[$switch->id] = 'offline'; // Default

    if (isset($poolResults[$id])) {
        $output = strtolower($poolResults[$id]->output());
        if (strpos($output, 'ttl=') !== false) {
            $newStatuses[$switch->id] = 'online';
        }
    }
}
echo json_encode($newStatuses);
