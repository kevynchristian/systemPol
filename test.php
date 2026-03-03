<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$roles = \App\Models\Role::where('hierarquia', 0)->get();
foreach($roles as $role) {
    echo $role->name . " -> Users: " . $role->users()->count() . PHP_EOL;
}
