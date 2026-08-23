<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::first();
echo "id: ".$u->id."\n";
echo "email: ".$u->email."\n";
echo "fullName: ".$u->fullName."\n";
echo "roles: ".json_encode($u->roles)."\n";
echo "permissions: ".json_encode($u->permissions)."\n";

echo "\n--- permissions table ---\n";
echo "count: ".\App\Models\Permission::count()."\n";

echo "\n--- roles table ---\n";
echo "count: ".\App\Models\Role::count()."\n";

echo "\n--- role_user table ---\n";
echo "count: ".\DB::table('role_user')->count()."\n";

echo "\n--- model_has_roles table ---\n";
echo "count: ".\DB::table('model_has_roles')->count()."\n";

echo "\n--- model_has_permissions table ---\n";
echo "count: ".\DB::table('model_has_permissions')->count()."\n";
</arg_value></tool_call>
