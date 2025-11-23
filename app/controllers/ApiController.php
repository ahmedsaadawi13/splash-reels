<?php
// FILE: /app/controllers/ApiController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/TenantApiKey.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/MediaFile.php';
require_once __DIR__ . '/../models/Clip.php';
require_once __DIR__ . '/../helpers/VideoPipelineHelper.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class ApiController extends Controller {

    private $tenantId = null;
    private $apiKeyRecord = null;

    private function authenticateApiKey() {
        $apiKey = Request::header('X-API-KEY');

        if (!$apiKey) {
            return $this->json(array(
                'success' => false,
                'message' => 'API key required'
            ), 401);
        }

        $apiKeyModel = new TenantApiKey();
        $this->apiKeyRecord = $apiKeyModel->findByKey($apiKey);

        if (!$this->apiKeyRecord) {
            return $this->json(array(
                'success' => false,
                'message' => 'Invalid API key'
            ), 401);
        }

        $this->tenantId = $this->apiKeyRecord['tenant_id'];

        // Update last used
        $apiKeyModel->updateLastUsed($this->apiKeyRecord['id']);

        // Increment API calls
        UsageHelper::incrementApiCalls($this->tenantId);

        return true;
    }

    public function createProject() {
        $auth = $this->authenticateApiKey();
        if ($auth !== true) {
            return $auth;
        }

        if (Request::method() !== 'POST') {
            return $this->json(array('success' => false, 'message' => 'Method not allowed'), 405);
        }

        $errors = ValidationHelper::validateData(Request::input(), array(
            'name' => 'required|min:2|max:255'
        ));

        if (!empty($errors)) {
            return $this->json(array(
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ), 400);
        }

        // Check quota
        $quotaCheck = UsageHelper::checkQuota($this->tenantId, 'projects', 1);
        if (!$quotaCheck['allowed']) {
            return $this->json(array(
                'success' => false,
                'message' => 'Project limit reached'
            ), 403);
        }

        $projectModel = new Project();

        $projectId = $projectModel->createProject(array(
            'tenant_id' => $this->tenantId,
            'name' => Request::input('name'),
            'description' => Request::input('description'),
            'status' => 'active',
            'created_by_user_id' => 1 // API created
        ));

        $project = $projectModel->find($projectId);

        return $this->json(array(
            'success' => true,
            'data' => $project
        ), 201);
    }

    public function createMedia() {
        $auth = $this->authenticateApiKey();
        if ($auth !== true) {
            return $auth;
        }

        if (Request::method() !== 'POST') {
            return $this->json(array('success' => false, 'message' => 'Method not allowed'), 405);
        }

        $errors = ValidationHelper::validateData(Request::input(), array(
            'project_id' => 'required|integer',
            'title' => 'required|min:2|max:255',
            'source_type' => 'required|in:youtube_url,other',
            'source_url' => 'required|url'
        ));

        if (!empty($errors)) {
            return $this->json(array(
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ), 400);
        }

        $projectId = Request::input('project_id');

        // Verify project belongs to tenant
        $projectModel = new Project();
        $project = $projectModel->find($projectId);

        if (!$project || $project['tenant_id'] != $this->tenantId) {
            return $this->json(array(
                'success' => false,
                'message' => 'Project not found'
            ), 404);
        }

        $metadata = FileUploadHelper::simulateVideoMetadata();

        $mediaModel = new MediaFile();

        $mediaId = $mediaModel->create(array(
            'tenant_id' => $this->tenantId,
            'project_id' => $projectId,
            'title' => Request::input('title'),
            'description' => Request::input('description'),
            'source_type' => Request::input('source_type'),
            'source_url' => Request::input('source_url'),
            'duration_seconds' => $metadata['duration_seconds'],
            'resolution' => $metadata['resolution'],
            'aspect_ratio' => $metadata['aspect_ratio'],
            'status' => 'uploaded',
            'created_by_user_id' => 1 // API created
        ));

        $mediaFile = $mediaModel->find($mediaId);

        return $this->json(array(
            'success' => true,
            'data' => $mediaFile
        ), 201);
    }

    public function generateClips($mediaFileId) {
        $auth = $this->authenticateApiKey();
        if ($auth !== true) {
            return $auth;
        }

        if (Request::method() !== 'POST') {
            return $this->json(array('success' => false, 'message' => 'Method not allowed'), 405);
        }

        $mediaModel = new MediaFile();
        $mediaFile = $mediaModel->find($mediaFileId);

        if (!$mediaFile || $mediaFile['tenant_id'] != $this->tenantId) {
            return $this->json(array(
                'success' => false,
                'message' => 'Media file not found'
            ), 404);
        }

        // Check quota
        $minutes = TimecodeHelper::getTotalMinutes($mediaFile['duration_seconds']);
        $quotaCheck = UsageHelper::checkQuota($this->tenantId, 'input_minutes', $minutes);

        if (!$quotaCheck['allowed']) {
            return $this->json(array(
                'success' => false,
                'message' => 'Monthly processing limit exceeded'
            ), 403);
        }

        $result = VideoPipelineHelper::generateClipSuggestions($mediaFileId);

        if ($result['success']) {
            UsageHelper::incrementInputMinutes($this->tenantId, $mediaFile['duration_seconds']);

            return $this->json(array(
                'success' => true,
                'message' => 'Clip suggestions generated',
                'count' => $result['count']
            ));
        }

        return $this->json(array(
            'success' => false,
            'message' => 'Failed to generate suggestions'
        ), 500);
    }

    public function getProjectClips($projectId) {
        $auth = $this->authenticateApiKey();
        if ($auth !== true) {
            return $auth;
        }

        if (Request::method() !== 'GET') {
            return $this->json(array('success' => false, 'message' => 'Method not allowed'), 405);
        }

        // Verify project belongs to tenant
        $projectModel = new Project();
        $project = $projectModel->find($projectId);

        if (!$project || $project['tenant_id'] != $this->tenantId) {
            return $this->json(array(
                'success' => false,
                'message' => 'Project not found'
            ), 404);
        }

        $clipModel = new Clip();
        $clips = $clipModel->getByProject($projectId);

        return $this->json(array(
            'success' => true,
            'data' => $clips,
            'total' => count($clips)
        ));
    }

    public function getClip($clipId) {
        $auth = $this->authenticateApiKey();
        if ($auth !== true) {
            return $auth;
        }

        if (Request::method() !== 'GET') {
            return $this->json(array('success' => false, 'message' => 'Method not allowed'), 405);
        }

        $clipModel = new Clip();
        $clip = $clipModel->find($clipId);

        if (!$clip || $clip['tenant_id'] != $this->tenantId) {
            return $this->json(array(
                'success' => false,
                'message' => 'Clip not found'
            ), 404);
        }

        return $this->json(array(
            'success' => true,
            'data' => $clip
        ));
    }
}
