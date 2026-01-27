<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking admin accounts in database..." . PHP_EOL;

$admins = App\Models\Admin::all();
echo "Found " . $admins->count() . " admin account(s):" . PHP_EOL;

foreach ($admins as $admin) {
    echo "- {$admin->email} ({$admin->name})" . PHP_EOL;
}

if ($admins->count() > 0) {
    echo PHP_EOL . "Deleting all existing admin accounts..." . PHP_EOL;
    App\Models\Admin::query()->delete();
    echo "All admin accounts deleted." . PHP_EOL;
}

echo PHP_EOL . "Creating new admin account..." . PHP_EOL;
$admin = App\Models\Admin::create([
    'name' => env('ADMIN_NAME', 'Admin User'),
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
    'is_super_admin' => true,
]);

echo "Admin account created successfully:" . PHP_EOL;
echo "- Email: {$admin->email}" . PHP_EOL;
echo "- Name: {$admin->name}" . PHP_EOL;
echo "- Super Admin: " . ($admin->is_super_admin ? 'Yes' : 'No') . PHP_EOL;