<?php
$ch = curl_init('http://localhost:8000/api/login');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['email' => 'testvnpt1700@example.com', 'password' => 'TestPassword123']),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$resp = curl_exec($ch);
curl_close($ch);
$loginData = json_decode($resp, true);
$token = $loginData['data']['token']['access_token'] ?? null;
echo "Got token\n";

// Save credentials
$ch2 = curl_init('http://localhost:8000/api/office-vnpt/credentials');
curl_setopt_array($ch2, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'username' => 'testuser123',
        'password' => 'TestPassword456',
        'device_name' => 'iPhone 17 Pro',
        'device_type' => 'IOS',
    ]),
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Content-Type: application/json', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$resp2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
echo "Save credentials (code=$httpCode2): " . substr($resp2, 0, 500) . "\n";

// Get credentials
$ch3 = curl_init('http://localhost:8000/api/office-vnpt/credentials');
curl_setopt_array($ch3, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$resp3 = curl_exec($ch3);
$httpCode3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
curl_close($ch3);
echo "Get credentials (code=$httpCode3): " . substr($resp3, 0, 500) . "\n";

// Delete credentials
$ch4 = curl_init('http://localhost:8000/api/office-vnpt/credentials');
curl_setopt_array($ch4, [
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$resp4 = curl_exec($ch4);
$httpCode4 = curl_getinfo($ch4, CURLINFO_HTTP_CODE);
curl_close($ch4);
echo "Delete credentials (code=$httpCode4): " . substr($resp4, 0, 500) . "\n";
?>
