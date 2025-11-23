<?php
// FILE: /app/helpers/FileUploadHelper.php

class FileUploadHelper {

    private static $allowedVideoMimes = array('video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo');
    private static $allowedImageMimes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
    private static $maxFileSize = 524288000; // 500MB in bytes

    public static function uploadVideo($file, $tenantId, $subfolder = 'raw') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array('success' => false, 'message' => 'No file uploaded');
        }

        // Validate file size
        if ($file['size'] > self::$maxFileSize) {
            return array('success' => false, 'message' => 'File size exceeds maximum allowed (500MB)');
        }

        // Validate MIME type (simulated - in production use finfo)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array('mp4', 'mov', 'webm', 'avi');

        if (!in_array($extension, $allowedExtensions)) {
            return array('success' => false, 'message' => 'Invalid file type. Allowed: mp4, mov, webm, avi');
        }

        // Create unique filename
        $filename = self::generateUniqueFilename($extension);

        // Create directory if not exists
        $uploadDir = self::getUploadPath($tenantId, $subfolder);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Full path
        $filePath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return array('success' => false, 'message' => 'Failed to save file');
        }

        // Return relative path for database storage
        $relativePath = "/storage/uploads/{$subfolder}/{$tenantId}/" . $filename;

        return array(
            'success' => true,
            'path' => $relativePath,
            'filename' => $filename,
            'size' => $file['size'],
            'original_name' => $file['name']
        );
    }

    public static function uploadImage($file, $tenantId, $subfolder = 'logos') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return array('success' => false, 'message' => 'No file uploaded');
        }

        // Validate file size (10MB for images)
        if ($file['size'] > 10485760) {
            return array('success' => false, 'message' => 'File size exceeds maximum allowed (10MB)');
        }

        // Validate MIME type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (!in_array($extension, $allowedExtensions)) {
            return array('success' => false, 'message' => 'Invalid file type. Allowed: jpg, png, gif, webp');
        }

        // Create unique filename
        $filename = self::generateUniqueFilename($extension);

        // Create directory if not exists
        $uploadDir = self::getUploadPath($tenantId, $subfolder);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Full path
        $filePath = $uploadDir . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            return array('success' => false, 'message' => 'Failed to save file');
        }

        // Return relative path for database storage
        $relativePath = "/storage/uploads/{$subfolder}/{$tenantId}/" . $filename;

        return array(
            'success' => true,
            'path' => $relativePath,
            'filename' => $filename,
            'size' => $file['size']
        );
    }

    private static function generateUniqueFilename($extension) {
        return uniqid('file_', true) . '_' . time() . '.' . $extension;
    }

    private static function getUploadPath($tenantId, $subfolder) {
        $basePath = __DIR__ . '/../../storage/uploads/' . $subfolder . '/' . $tenantId;
        return $basePath;
    }

    public static function getFileSizeMB($bytes) {
        return round($bytes / 1048576, 2);
    }

    public static function deleteFile($path) {
        $fullPath = __DIR__ . '/../../' . ltrim($path, '/');
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public static function simulateVideoMetadata() {
        // Simulated metadata extraction (in production, use FFmpeg)
        return array(
            'duration_seconds' => rand(300, 3600),
            'resolution' => '1920x1080',
            'aspect_ratio' => '16:9'
        );
    }
}
