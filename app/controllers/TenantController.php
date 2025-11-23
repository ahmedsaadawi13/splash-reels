<?php
// FILE: /app/controllers/TenantController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Tenant.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/TenantSubscription.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class TenantController extends Controller {

    public function index() {
        $this->requireAuth();
        $this->requireRole(array('platform_admin'));

        $tenantModel = new Tenant();
        $tenants = $tenantModel->all(array(), 'created_at DESC');

        return $this->view('admin/tenants', array('tenants' => $tenants));
    }

    public function view($id) {
        $this->requireAuth();
        $this->requireRole(array('platform_admin'));

        $tenantModel = new Tenant();
        $tenant = $tenantModel->find($id);

        if (!$tenant) {
            Session::setFlash('error', 'Tenant not found');
            return $this->redirect('/admin/tenants');
        }

        $db = Database::getInstance();

        // Get subscription info
        $subscription = $db->fetchOne(
            "SELECT ts.*, p.name as plan_name FROM tenant_subscriptions ts
             INNER JOIN plans p ON ts.plan_id = p.id
             WHERE ts.tenant_id = ? AND ts.status = 'active'
             LIMIT 1",
            array($id)
        );

        // Get usage
        $usage = $db->fetchOne(
            "SELECT * FROM tenant_usage WHERE tenant_id = ? AND month = ? LIMIT 1",
            array($id, date('Y-m'))
        );

        // Get user count
        $userCount = $db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE tenant_id = ?",
            array($id)
        );

        return $this->view('admin/tenant-view', array(
            'tenant' => $tenant,
            'subscription' => $subscription,
            'usage' => $usage,
            'userCount' => $userCount
        ));
    }

    public function updateStatus($id) {
        $this->requireAuth();
        $this->requireRole(array('platform_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/admin/tenants');
        }

        $this->validateCSRF();

        $tenantModel = new Tenant();
        $tenant = $tenantModel->find($id);

        if (!$tenant) {
            Session::setFlash('error', 'Tenant not found');
            return $this->redirect('/admin/tenants');
        }

        $status = Request::post('status');

        if (!in_array($status, array('active', 'suspended', 'canceled'))) {
            Session::setFlash('error', 'Invalid status');
            return $this->redirect('/admin/tenants');
        }

        $tenantModel->update($id, array('status' => $status));

        Session::setFlash('success', 'Tenant status updated');
        return $this->redirect('/admin/tenants');
    }
}
