<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ONLY TITLE LIKE 'm%' ===\n";
foreach (App\Models\Book::where('title', 'like', 'm%')->get() as $b) {
    echo "- " . $b->title . " (Author: " . $b->author . ")\n";
}

echo "\n=== TITLE OR AUTHOR LIKE 'm%' ===\n";
foreach (App\Models\Book::where(function($q){ $q->where('title', 'like', 'm%')->orWhere('author', 'like', 'm%'); })->get() as $b) {
    echo "- " . $b->title . " (Author: " . $b->author . ")\n";
}
