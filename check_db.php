<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Eatery::whereHas('category', function($q) {
    $q->where('slug', 'dac-san-dia-phuong');
})->count();

$names = \App\Models\Eatery::whereHas('category', function($q) {
    $q->where('slug', 'dac-san-dia-phuong');
})->pluck('name')->toArray();

echo "Count: $count\n";
print_r($names);
