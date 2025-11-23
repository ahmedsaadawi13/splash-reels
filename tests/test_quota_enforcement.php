<?php
// FILE: /tests/test_quota_enforcement.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/helpers/UsageHelper.php';
require_once __DIR__ . '/../app/helpers/TimecodeHelper.php';

try {
    $tenantId = 1;

    // Get current usage and limits
    $usage = UsageHelper::getCurrentUsage($tenantId);
    $limits = UsageHelper::getPlanLimits($tenantId);

    echo "Current Usage:\n";
    echo "  Input Minutes: {$usage['total_input_minutes']} / {$limits['max_minutes_input_per_month']}\n";
    echo "  Exports: {$usage['total_exported_clips']} / {$limits['max_exports_per_month']}\n";
    echo "  Storage: {$usage['total_storage_mb']} MB / {$limits['storage_limit_mb']} MB\n";

    // Test quota check
    $quotaCheck = UsageHelper::checkQuota($tenantId, 'input_minutes', 10);

    if (isset($quotaCheck['allowed'])) {
        echo "OK - Quota check returned: " . ($quotaCheck['allowed'] ? 'ALLOWED' : 'DENIED') . "\n";
    } else {
        echo "FAIL - Quota check failed\n";
    }

    // Test storage quota
    $storageCheck = UsageHelper::checkQuota($tenantId, 'storage', 100);

    if (isset($storageCheck['allowed'])) {
        echo "OK - Storage quota check returned: " . ($storageCheck['allowed'] ? 'ALLOWED' : 'DENIED') . "\n";
    } else {
        echo "FAIL - Storage quota check failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
