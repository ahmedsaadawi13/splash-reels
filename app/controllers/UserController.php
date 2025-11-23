<?php
// FILE: /app/controllers/UserController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class UserController extends Controller {

    public function index() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        $tenantId = $this->getTenantId();
        $userModel = new User();

        $users = $userModel->getByTenant($tenantId);

        return $this->view('users/index', array('users' => $users));
    }

    public function create() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'email' => 'required|email',
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'password' => 'required|min:8',
                'role' => 'required|in:tenant_admin,editor,viewer'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $tenantId = $this->getTenantId();
            $userModel = new User();

            // Check if email exists
            if ($userModel->findByEmail(Request::post('email'))) {
                Session::setFlash('error', 'Email already exists');
                return $this->back();
            }

            $userId = $userModel->createUser(array(
                'tenant_id' => $tenantId,
                'email' => Request::post('email'),
                'password' => Request::post('password'),
                'first_name' => Request::post('first_name'),
                'last_name' => Request::post('last_name'),
                'role' => Request::post('role'),
                'status' => 'active'
            ));

            Auth::logActivity($tenantId, $this->getUserId(), 'create_user', 'user', $userId);

            Session::setFlash('success', 'User created successfully');
            return $this->redirect('/users');
        }

        return $this->view('users/create');
    }

    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        $tenantId = $this->getTenantId();
        $userModel = new User();

        $user = $userModel->find($id);

        if (!$user || $user['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'User not found');
            return $this->redirect('/users');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'role' => 'required|in:tenant_admin,editor,viewer',
                'status' => 'required|in:active,inactive,suspended'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $data = array(
                'first_name' => Request::post('first_name'),
                'last_name' => Request::post('last_name'),
                'role' => Request::post('role'),
                'status' => Request::post('status')
            );

            // Update password if provided
            $password = Request::post('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }

            $userModel->updateUser($id, $data);

            Auth::logActivity($tenantId, $this->getUserId(), 'update_user', 'user', $id);

            Session::setFlash('success', 'User updated successfully');
            return $this->redirect('/users');
        }

        return $this->view('users/edit', array('editUser' => $user));
    }

    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/users');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $userModel = new User();

        $user = $userModel->find($id);

        if (!$user || $user['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'User not found');
            return $this->redirect('/users');
        }

        // Prevent self-deletion
        if ($id == $this->getUserId()) {
            Session::setFlash('error', 'You cannot delete yourself');
            return $this->redirect('/users');
        }

        $userModel->delete($id);

        Auth::logActivity($tenantId, $this->getUserId(), 'delete_user', 'user', $id);

        Session::setFlash('success', 'User deleted successfully');
        return $this->redirect('/users');
    }
}
