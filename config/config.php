<?php
// FILE: /config/config.php

// Load .env file if exists
if (file_exists(__DIR__ . '/../.env')) {
    $envFile = file_get_contents(__DIR__ . '/../.env');
    $lines = explode("\n", $envFile);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Configuration array
return array(
    'app' => array(
        'name' => getenv('APP_NAME') ?: 'SplashReels',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') === 'true',
        'url' => getenv('APP_URL') ?: 'http://localhost'
    ),
    'session' => array(
        'lifetime' => getenv('SESSION_LIFETIME') ?: 7200
    ),
    'upload' => array(
        'max_size' => getenv('MAX_UPLOAD_SIZE') ?: 524288000,
        'allowed_video_extensions' => explode(',', getenv('ALLOWED_VIDEO_EXTENSIONS') ?: 'mp4,mov,webm,avi')
    ),
    'storage' => array(
        'path' => getenv('STORAGE_PATH') ?: '/storage/uploads'
    )
);
