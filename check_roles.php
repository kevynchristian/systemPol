<?php
use Spatie\Permission\Models\Role;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Role::orderBy('hierarquia')->get(['name', 'hierarquia'])->toArray();
file_put_contents('roles_output.json', json_encode($roles, JSON_PRETTY_PRINT));
