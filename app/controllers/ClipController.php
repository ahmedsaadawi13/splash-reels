<?php
// FILE: /app/controllers/ClipController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Clip.php';
require_once __DIR__ . '/../models/ClipSuggestion.php';
require_once __DIR__ . '/../models/MediaFile.php';
require_once __DIR__ . '/../models/ClipTemplate.php';
require_once __DIR__ . '/../models/Caption.php';
require_once __DIR__ . '/../models/Export.php';
require_once __DIR__ . '/../helpers/VideoPipelineHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';

class ClipController extends Controller {

    public function index() {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $clipModel = new Clip();

        $projectId = Request::get('project_id');

        if ($projectId) {
            $clips = $clipModel->getByProject($projectId);
        } else {
            $clips = $clipModel->getByTenant($tenantId);
        }

        return $this->view('clips/index', array(
            'clips' => $clips,
            'projectId' => $projectId
        ));
    }

    public function suggestions($mediaFileId) {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $mediaModel = new MediaFile();
        $suggestionModel = new ClipSuggestion();

        $mediaFile = $mediaModel->find($mediaFileId);

        if (!$mediaFile || $mediaFile['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Media file not found');
            return $this->redirect('/media');
        }

        $suggestions = $suggestionModel->getByMediaFile($mediaFileId);

        return $this->view('clips/suggestions', array(
            'mediaFile' => $mediaFile,
            'suggestions' => $suggestions
        ));
    }

    public function acceptSuggestion($suggestionId) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/clips');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $suggestionModel = new ClipSuggestion();
        $mediaModel = new MediaFile();
        $clipModel = new Clip();

        $suggestion = $suggestionModel->find($suggestionId);

        if (!$suggestion || $suggestion['tenant_id'] != $tenantId) {
            return $this->json(array('success' => false, 'message' => 'Suggestion not found'), 404);
        }

        $mediaFile = $mediaModel->find($suggestion['media_file_id']);

        if (!$mediaFile) {
            return $this->json(array('success' => false, 'message' => 'Media file not found'), 404);
        }

        // Create clip from suggestion
        $clipId = $clipModel->create(array(
            'tenant_id' => $tenantId,
            'project_id' => $mediaFile['project_id'],
            'media_file_id' => $mediaFile['id'],
            'clip_template_id' => Request::post('template_id'),
            'title' => $suggestion['suggested_title'],
            'description' => $suggestion['suggested_caption_text'],
            'start_seconds' => $suggestion['start_seconds'],
            'end_seconds' => $suggestion['end_seconds'],
            'duration_seconds' => $suggestion['end_seconds'] - $suggestion['start_seconds'],
            'status' => 'draft',
            'platform_hint' => Request::post('platform_hint', 'generic'),
            'created_by_user_id' => $this->getUserId()
        ));

        // Update suggestion status
        $suggestionModel->update($suggestionId, array('status' => 'accepted'));

        Auth::logActivity($tenantId, $this->getUserId(), 'accept_suggestion', 'clip_suggestion', $suggestionId);

        return $this->json(array(
            'success' => true,
            'clip_id' => $clipId,
            'redirect' => '/clips/edit/' . $clipId
        ));
    }

    public function create() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();
        $db = Database::getInstance();

        $projects = $db->fetchAll(
            "SELECT * FROM projects WHERE tenant_id = ? AND status = 'active' ORDER BY name",
            array($tenantId)
        );

        $templateModel = new ClipTemplate();
        $templates = $templateModel->getAllAvailable($tenantId);

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'project_id' => 'required|integer',
                'media_file_id' => 'required|integer',
                'title' => 'required|min:2',
                'start_seconds' => 'required|integer',
                'end_seconds' => 'required|integer'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $startSeconds = (int) Request::post('start_seconds');
            $endSeconds = (int) Request::post('end_seconds');

            if ($endSeconds <= $startSeconds) {
                Session::setFlash('error', 'End time must be after start time');
                return $this->back();
            }

            $clipModel = new Clip();

            $clipId = $clipModel->create(array(
                'tenant_id' => $tenantId,
                'project_id' => Request::post('project_id'),
                'media_file_id' => Request::post('media_file_id'),
                'clip_template_id' => Request::post('clip_template_id'),
                'title' => Request::post('title'),
                'description' => Request::post('description'),
                'start_seconds' => $startSeconds,
                'end_seconds' => $endSeconds,
                'duration_seconds' => $endSeconds - $startSeconds,
                'status' => 'draft',
                'platform_hint' => Request::post('platform_hint', 'generic'),
                'created_by_user_id' => $this->getUserId()
            ));

            Auth::logActivity($tenantId, $this->getUserId(), 'create_clip', 'clip', $clipId);

            Session::setFlash('success', 'Clip created successfully');
            return $this->redirect('/clips/edit/' . $clipId);
        }

        return $this->view('clips/create', array(
            'projects' => $projects,
            'templates' => $templates
        ));
    }

    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();
        $clipModel = new Clip();
        $templateModel = new ClipTemplate();

        $clip = $clipModel->find($id);

        if (!$clip || $clip['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Clip not found');
            return $this->redirect('/clips');
        }

        $templates = $templateModel->getAllAvailable($tenantId);

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'title' => 'required|min:2',
                'start_seconds' => 'required|integer',
                'end_seconds' => 'required|integer'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $startSeconds = (int) Request::post('start_seconds');
            $endSeconds = (int) Request::post('end_seconds');

            if ($endSeconds <= $startSeconds) {
                Session::setFlash('error', 'End time must be after start time');
                return $this->back();
            }

            $clipModel->update($id, array(
                'title' => Request::post('title'),
                'description' => Request::post('description'),
                'start_seconds' => $startSeconds,
                'end_seconds' => $endSeconds,
                'duration_seconds' => $endSeconds - $startSeconds,
                'clip_template_id' => Request::post('clip_template_id'),
                'platform_hint' => Request::post('platform_hint')
            ));

            Session::setFlash('success', 'Clip updated successfully');
            return $this->back();
        }

        return $this->view('clips/edit', array(
            'clip' => $clip,
            'templates' => $templates
        ));
    }

    public function render($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/clips');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $clipModel = new Clip();

        $clip = $clipModel->find($id);

        if (!$clip || $clip['tenant_id'] != $tenantId) {
            return $this->json(array('success' => false, 'message' => 'Clip not found'), 404);
        }

        $result = VideoPipelineHelper::renderClip($id);

        if ($result['success']) {
            Auth::logActivity($tenantId, $this->getUserId(), 'render_clip', 'clip', $id);

            return $this->json(array(
                'success' => true,
                'message' => 'Clip rendered successfully',
                'output_path' => $result['output_path']
            ));
        }

        return $this->json(array('success' => false, 'message' => 'Failed to render clip'), 500);
    }

    public function export($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/clips');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $clipModel = new Clip();

        $clip = $clipModel->find($id);

        if (!$clip || $clip['tenant_id'] != $tenantId) {
            return $this->json(array('success' => false, 'message' => 'Clip not found'), 404);
        }

        // Check export quota
        $quotaCheck = UsageHelper::checkQuota($tenantId, 'exports', 1);

        if (!$quotaCheck['allowed']) {
            return $this->json(array(
                'success' => false,
                'message' => 'Monthly export limit exceeded. Please upgrade your plan.'
            ), 403);
        }

        $result = VideoPipelineHelper::exportClip($id);

        if ($result['success']) {
            Auth::logActivity($tenantId, $this->getUserId(), 'export_clip', 'clip', $id);

            return $this->json(array(
                'success' => true,
                'message' => 'Export ready for download',
                'download_url' => $result['download_url']
            ));
        }

        return $this->json(array('success' => false, 'message' => 'Export failed'), 500);
    }

    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (!Request::isPost()) {
            return $this->redirect('/clips');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $clipModel = new Clip();

        $clip = $clipModel->find($id);

        if (!$clip || $clip['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Clip not found');
            return $this->redirect('/clips');
        }

        $clipModel->delete($id);

        Auth::logActivity($tenantId, $this->getUserId(), 'delete_clip', 'clip', $id);

        Session::setFlash('success', 'Clip deleted successfully');
        return $this->redirect('/clips');
    }
}
