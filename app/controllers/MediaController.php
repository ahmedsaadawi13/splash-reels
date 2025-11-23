<?php
// FILE: /app/controllers/MediaController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/MediaFile.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/ClipSuggestion.php';
require_once __DIR__ . '/../helpers/FileUploadHelper.php';
require_once __DIR__ . '/../helpers/VideoPipelineHelper.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class MediaController extends Controller {

    public function index() {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $mediaModel = new MediaFile();

        $projectId = Request::get('project_id');

        if ($projectId) {
            $mediaFiles = $mediaModel->getByProject($projectId);
        } else {
            $mediaFiles = $mediaModel->getByTenant($tenantId);
        }

        return $this->view('media/index', array(
            'mediaFiles' => $mediaFiles,
            'projectId' => $projectId
        ));
    }

    public function upload() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();

        $projectModel = new Project();
        $projects = $projectModel->getByTenant($tenantId, 'active');

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'project_id' => 'required|integer',
                'title' => 'required|min:2|max:255'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $projectId = Request::post('project_id');

            // Verify project belongs to tenant
            $project = $projectModel->find($projectId);
            if (!$project || $project['tenant_id'] != $tenantId) {
                Session::setFlash('error', 'Invalid project');
                return $this->back();
            }

            $sourceType = Request::post('source_type', 'upload');
            $mediaModel = new MediaFile();

            if ($sourceType === 'upload') {
                $file = Request::file('video_file');

                if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                    Session::setFlash('error', 'Please select a video file');
                    return $this->back();
                }

                // Check storage quota
                $fileSizeMB = FileUploadHelper::getFileSizeMB($file['size']);
                $quotaCheck = UsageHelper::checkQuota($tenantId, 'storage', $fileSizeMB);

                if (!$quotaCheck['allowed']) {
                    Session::setFlash('error', 'Storage limit exceeded. Please upgrade your plan.');
                    return $this->back();
                }

                $uploadResult = FileUploadHelper::uploadVideo($file, $tenantId, 'raw');

                if (!$uploadResult['success']) {
                    Session::setFlash('error', $uploadResult['message']);
                    return $this->back();
                }

                $metadata = FileUploadHelper::simulateVideoMetadata();

                $mediaId = $mediaModel->create(array(
                    'tenant_id' => $tenantId,
                    'project_id' => $projectId,
                    'title' => Request::post('title'),
                    'description' => Request::post('description'),
                    'source_type' => 'upload',
                    'file_path' => $uploadResult['path'],
                    'duration_seconds' => $metadata['duration_seconds'],
                    'resolution' => $metadata['resolution'],
                    'aspect_ratio' => $metadata['aspect_ratio'],
                    'file_size_mb' => $fileSizeMB,
                    'status' => 'uploaded',
                    'created_by_user_id' => $this->getUserId()
                ));

                // Update storage usage
                UsageHelper::incrementStorage($tenantId, $fileSizeMB);

            } else if ($sourceType === 'youtube_url') {
                $sourceUrl = Request::post('source_url');

                if (!ValidationHelper::url($sourceUrl)) {
                    Session::setFlash('error', 'Invalid URL');
                    return $this->back();
                }

                $metadata = FileUploadHelper::simulateVideoMetadata();

                $mediaId = $mediaModel->create(array(
                    'tenant_id' => $tenantId,
                    'project_id' => $projectId,
                    'title' => Request::post('title'),
                    'description' => Request::post('description'),
                    'source_type' => 'youtube_url',
                    'source_url' => $sourceUrl,
                    'duration_seconds' => $metadata['duration_seconds'],
                    'resolution' => $metadata['resolution'],
                    'aspect_ratio' => $metadata['aspect_ratio'],
                    'status' => 'uploaded',
                    'created_by_user_id' => $this->getUserId()
                ));
            }

            Auth::logActivity($tenantId, $this->getUserId(), 'upload_media', 'media_file', $mediaId);

            Session::setFlash('success', 'Media uploaded successfully');
            return $this->redirect('/media/view/' . $mediaId);
        }

        return $this->view('media/upload', array('projects' => $projects));
    }

    public function view($id) {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $mediaModel = new MediaFile();
        $suggestionModel = new ClipSuggestion();

        $mediaFile = $mediaModel->find($id);

        if (!$mediaFile || $mediaFile['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Media file not found');
            return $this->redirect('/media');
        }

        $suggestions = $suggestionModel->getByMediaFile($id);

        return $this->view('media/view', array(
            'mediaFile' => $mediaFile,
            'suggestions' => $suggestions
        ));
    }

    public function generateClips($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/media');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $mediaModel = new MediaFile();

        $mediaFile = $mediaModel->find($id);

        if (!$mediaFile || $mediaFile['tenant_id'] != $tenantId) {
            return $this->json(array('success' => false, 'message' => 'Media file not found'), 404);
        }

        // Check input minutes quota
        $minutes = TimecodeHelper::getTotalMinutes($mediaFile['duration_seconds']);
        $quotaCheck = UsageHelper::checkQuota($tenantId, 'input_minutes', $minutes);

        if (!$quotaCheck['allowed']) {
            return $this->json(array(
                'success' => false,
                'message' => 'Monthly processing limit exceeded. Please upgrade your plan.'
            ), 403);
        }

        // Generate AI highlights
        $result = VideoPipelineHelper::generateClipSuggestions($id);

        if ($result['success']) {
            // Increment usage
            UsageHelper::incrementInputMinutes($tenantId, $mediaFile['duration_seconds']);

            Auth::logActivity($tenantId, $this->getUserId(), 'generate_clips', 'media_file', $id);

            return $this->json(array(
                'success' => true,
                'message' => 'Clip suggestions generated successfully',
                'count' => $result['count']
            ));
        }

        return $this->json(array('success' => false, 'message' => 'Failed to generate suggestions'), 500);
    }

    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/media');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $mediaModel = new MediaFile();

        $mediaFile = $mediaModel->find($id);

        if (!$mediaFile || $mediaFile['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Media file not found');
            return $this->redirect('/media');
        }

        // Delete file if exists
        if ($mediaFile['file_path']) {
            FileUploadHelper::deleteFile($mediaFile['file_path']);
        }

        $mediaModel->delete($id);

        Auth::logActivity($tenantId, $this->getUserId(), 'delete_media', 'media_file', $id);

        Session::setFlash('success', 'Media file deleted successfully');
        return $this->redirect('/media');
    }
}
