<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$a = App\Models\Activity::find(2);
if ($a) {
    echo "ID: {$a->id}\nTitle: {$a->title}\nCreated: {$a->created_at}\nUpdated: {$a->updated_at}\n";
} else {
    echo "Activity 2 not found\n";
}
