<?php
// FILE: /tests/test_create_clip.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/Clip.php';
require_once __DIR__ . '/../app/helpers/VideoPipelineHelper.php';

try {
    $clipModel = new Clip();

    $clipId = $clipModel->create(array(
        'tenant_id' => 1,
        'project_id' => 1,
        'media_file_id' => 1,
        'title' => 'Test Clip ' . time(),
        'description' => 'Test clip created by automated test',
        'start_seconds' => 10,
        'end_seconds' => 40,
        'duration_seconds' => 30,
        'status' => 'draft',
        'platform_hint' => 'generic',
        'created_by_user_id' => 2
    ));

    if ($clipId > 0) {
        echo "OK - Clip created with ID: $clipId\n";

        // Test rendering
        $result = VideoPipelineHelper::renderClip($clipId);

        if ($result['success']) {
            echo "OK - Clip rendered successfully\n";
        } else {
            echo "FAIL - Clip rendering failed\n";
        }

        // Clean up
        $clipModel->delete($clipId);
        echo "OK - Test clip cleaned up\n";
    } else {
        echo "FAIL - Clip creation failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
