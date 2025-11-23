<?php
// FILE: /tests/test_auth_login.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/Auth.php';

try {
    // Test password verification
    $testEmail = 'editor@demo-agency.com';
    $testPassword = 'password';

    $db = Database::getInstance();
    $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", array($testEmail));

    if (!$user) {
        echo "FAIL - Test user not found\n";
        exit;
    }

    if (password_verify($testPassword, $user['password_hash'])) {
        echo "OK - Password verification successful\n";
    } else {
        echo "FAIL - Password verification failed\n";
    }

    // Test Auth::attempt
    $result = Auth::attempt($testEmail, $testPassword);

    if ($result['success']) {
        echo "OK - Auth::attempt successful\n";
    } else {
        echo "FAIL - Auth::attempt failed: " . $result['message'] . "\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
