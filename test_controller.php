<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $c = app(App\Http\Controllers\ITSas\SwitchMonitoringController::class);
    $res = $c->status();
    echo $res->getContent();
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}
