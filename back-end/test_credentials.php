<?php
// Bootstrap Laravel app properly
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Auth Flow ===\n";

// 1. Check JWT config
echo "JWT_SECRET: " . (config('jwt.secret') ? 'SET (' . substr(config('jwt.secret'), 0, 15) . '...)' : 'NOT SET') . "\n";
echo "AUTH_MODEL: " . config('auth.providers.users.model') . "\n";
echo "Guard: " . config('auth.defaults.guard') . "\n";

// 2. Get user
$u = App\Infrastructure\Models\User::latest()->first();
echo "Latest user: " . ($u ? $u->email . " (id=" . $u->id . ")" : 'none') . "\n";

// 3. Create JWT token
if ($u) {
    $token = Tymon\JWTAuth\Facades\JWTAuth::fromUser($u);
    echo "Token created: " . substr($token, 0, 50) . "...\n";
    
    // 4. Verify token
    $verified = Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
    echo "Token verified: " . ($verified ? 'YES (user_id=' . $verified->id . ')' : 'NO') . "\n";
    
    // 5. Try to save credentials
    echo "\n=== Testing Credential Save ===\n";
    $svc = app(App\Services\VnptCredentialService::class);
    try {
        $result = $svc->save($u->id, [
            'username' => 'testuser123',
            'password' => 'TestPassword456',
            'device_name' => 'iPhone 17 Pro',
            'device_type' => 'IOS',
        ]);
        echo "Save result: " . json_encode($result) . "\n";
    } catch (Exception $e) {
        echo "Save ERROR: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
    
    // 6. Check DB
    echo "\n=== Checking DB ===\n";
    $count = App\Infrastructure\Models\VnptCredential::count();
    echo "VnptCredential count: $count\n";
    if ($count > 0) {
        $creds = App\Infrastructure\Models\VnptCredential::all();
        foreach ($creds as $c) {
            echo "Credential: user_id=$c->user_id, username=$c->username, device=$c->device_name\n";
        }
    }
    
    // 7. Get credentials
    echo "\n=== Testing Get Credentials ===\n";
    $gotten = $svc->getByUser($u->id);
    echo "Get result: " . json_encode($gotten) . "\n";
}
?>
