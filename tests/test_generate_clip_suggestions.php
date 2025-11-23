<?php
// FILE: /tests/test_generate_clip_suggestions.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/MediaFile.php';
require_once __DIR__ . '/../app/models/ClipSuggestion.php';
require_once __DIR__ . '/../app/helpers/VideoPipelineHelper.php';
require_once __DIR__ . '/../app/helpers/TimecodeHelper.php';

try {
    $mediaFileId = 1; // Use demo media file from seed data

    $result = VideoPipelineHelper::generateClipSuggestions($mediaFileId);

    if ($result['success'] && $result['count'] > 0) {
        echo "OK - Generated {$result['count']} clip suggestions\n";
    } else {
        echo "FAIL - Clip suggestion generation failed\n";
    }

} catch (Exception $e) {
    echo "FAIL - Test error: " . $e->getMessage() . "\n";
}
