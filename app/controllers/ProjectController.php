<?php
// FILE: /app/controllers/ProjectController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/MediaFile.php';
require_once __DIR__ . '/../models/Clip.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/SlugHelper.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';

class ProjectController extends Controller {

    public function index() {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $projectModel = new Project();

        $status = Request::get('status', 'active');
        $projects = $projectModel->getByTenant($tenantId, $status);

        return $this->view('projects/index', array(
            'projects' => $projects,
            'currentStatus' => $status
        ));
    }

    public function view($id) {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $projectModel = new Project();
        $mediaModel = new MediaFile();
        $clipModel = new Clip();

        $project = $projectModel->find($id);

        if (!$project || $project['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Project not found');
            return $this->redirect('/projects');
        }

        $mediaFiles = $mediaModel->getByProject($id);
        $clips = $clipModel->getByProject($id);
        $stats = $projectModel->getStats($id);

        return $this->view('projects/view', array(
            'project' => $project,
            'mediaFiles' => $mediaFiles,
            'clips' => $clips,
            'stats' => $stats
        ));
    }

    public function create() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();

        // Check quota
        $quotaCheck = UsageHelper::checkQuota($tenantId, 'projects', 1);
        if (!$quotaCheck['allowed']) {
            Session::setFlash('error', 'Project limit reached. Please upgrade your plan.');
            return $this->redirect('/projects');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'name' => 'required|min:2|max:255',
                'description' => 'max:1000'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                Session::set('form_data', $_POST);
                return $this->back();
            }

            $projectModel = new Project();

            $projectId = $projectModel->createProject(array(
                'tenant_id' => $tenantId,
                'name' => Request::post('name'),
                'description' => Request::post('description'),
                'status' => 'active',
                'created_by_user_id' => $this->getUserId()
            ));

            Auth::logActivity($tenantId, $this->getUserId(), 'create_project', 'project', $projectId);

            Session::setFlash('success', 'Project created successfully');
            return $this->redirect('/projects/' . $projectId);
        }

        return $this->view('projects/create');
    }

    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();
        $projectModel = new Project();

        $project = $projectModel->find($id);

        if (!$project || $project['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Project not found');
            return $this->redirect('/projects');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'name' => 'required|min:2|max:255',
                'description' => 'max:1000',
                'status' => 'in:active,archived'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $projectModel->update($id, array(
                'name' => Request::post('name'),
                'description' => Request::post('description'),
                'status' => Request::post('status')
            ));

            Auth::logActivity($tenantId, $this->getUserId(), 'update_project', 'project', $id);

            Session::setFlash('success', 'Project updated successfully');
            return $this->redirect('/projects/' . $id);
        }

        return $this->view('projects/edit', array('project' => $project));
    }

    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/projects');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $projectModel = new Project();

        $project = $projectModel->find($id);

        if (!$project || $project['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Project not found');
            return $this->redirect('/projects');
        }

        $projectModel->delete($id);

        Auth::logActivity($tenantId, $this->getUserId(), 'delete_project', 'project', $id);

        Session::setFlash('success', 'Project deleted successfully');
        return $this->redirect('/projects');
    }
}
