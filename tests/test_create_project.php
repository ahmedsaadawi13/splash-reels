<?php
// FILE: /tests/test_create_project.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/helpers/SlugHelper.php';

try {
    $projectModel = new Project();

    $testProjectName = 'Test Project ' . time();
    $projectId = $projectModel->createProject(array(
        'tenant_id' => 1,
        'name' => $testProjectName,
        'description' => 'Test project created by automated test',
        'status' => 'active',
        'created_by_user_id' => 2
    ));

    if ($projectId > 0) {
        echo "OK - Project created with ID: $projectId\n";

        // Clean up
        $projectModel->delete($projectId);
        echo "OK - Test project cleaned up\n";
    } else {
        echo "FAIL - Project creation failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
