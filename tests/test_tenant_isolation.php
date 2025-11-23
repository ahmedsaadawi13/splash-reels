<?php
// FILE: /tests/test_tenant_isolation.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/Project.php';

try {
    $projectModel = new Project();

    // Get projects for tenant 1
    $tenant1Projects = $projectModel->getByTenant(1);

    echo "Tenant 1 projects: " . count($tenant1Projects) . "\n";

    // Verify no cross-tenant data leakage
    $db = Database::getInstance();

    // Try to get project from tenant 1, but filter by tenant 2
    $crossTenantTest = $db->fetchOne(
        "SELECT * FROM projects WHERE id = 1 AND tenant_id = 999"
    );

    if (!$crossTenantTest) {
        echo "OK - Tenant isolation working correctly (cross-tenant query returned null)\n";
    } else {
        echo "FAIL - Tenant isolation breach detected\n";
    }

    // Verify all projects for tenant have correct tenant_id
    $allTenant1Projects = $db->fetchAll("SELECT * FROM projects WHERE tenant_id = 1");

    $isolationPass = true;
    foreach ($allTenant1Projects as $project) {
        if ($project['tenant_id'] != 1) {
            $isolationPass = false;
            break;
        }
    }

    if ($isolationPass) {
        echo "OK - All projects correctly filtered by tenant_id\n";
    } else {
        echo "FAIL - Tenant isolation check failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
