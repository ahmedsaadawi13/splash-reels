<?php
// FILE: /tests/test_api_key_auth.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/TenantApiKey.php';

try {
    $apiKeyModel = new TenantApiKey();

    // Get demo API key from seed data
    $apiKey = $apiKeyModel->findByKey('sk_live_demo_1234567890abcdef1234567890abcdef12345678');

    if ($apiKey) {
        echo "OK - API key found\n";
        echo "  Tenant ID: {$apiKey['tenant_id']}\n";
        echo "  Label: {$apiKey['label']}\n";
        echo "  Active: " . ($apiKey['is_active'] ? 'Yes' : 'No') . "\n";
        echo "  Rate Limit: {$apiKey['rate_limit_per_minute']}/min\n";
    } else {
        echo "FAIL - API key not found\n";
    }

    // Test invalid API key
    $invalidKey = $apiKeyModel->findByKey('sk_invalid_key');

    if (!$invalidKey) {
        echo "OK - Invalid API key correctly rejected\n";
    } else {
        echo "FAIL - Invalid API key was accepted\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
