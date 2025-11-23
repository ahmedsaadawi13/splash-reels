<?php
// FILE: /tests/test_upload_media_stub.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/MediaFile.php';

try {
    $mediaModel = new MediaFile();

    $mediaId = $mediaModel->create(array(
        'tenant_id' => 1,
        'project_id' => 1,
        'title' => 'Test Media ' . time(),
        'description' => 'Test media file',
        'source_type' => 'upload',
        'file_path' => '/storage/uploads/raw/1/test.mp4',
        'duration_seconds' => 600,
        'resolution' => '1920x1080',
        'aspect_ratio' => '16:9',
        'file_size_mb' => 100.50,
        'status' => 'uploaded',
        'created_by_user_id' => 2
    ));

    if ($mediaId > 0) {
        echo "OK - Media file created with ID: $mediaId\n";

        // Clean up
        $mediaModel->delete($mediaId);
        echo "OK - Test media cleaned up\n";
    } else {
        echo "FAIL - Media file creation failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
