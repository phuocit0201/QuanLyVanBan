<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "JWT_SECRET: " . (env('JWT_SECRET') ? 'SET (' . substr(env('JWT_SECRET'), 0, 20) . '...)' : 'NOT SET') . "\n";
echo "AUTH_MODEL: " . env('AUTH_MODEL', 'not set') . "\n";
echo "UserEntity: " . App\Domain\Entities\UserEntity::class . "\n";
$u = App\Infrastructure\Models\User::latest()->first();
echo "Latest user: " . ($u ? $u->email : 'none') . "\n";
if ($u) {
    $token = Tymon\JWTAuth\Facades\JWTAuth::fromUser($u);
    echo "Token: " . $token . "\n";
    echo "Verify: " . (Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate() ? 'OK' : 'FAIL') . "\n";
}
