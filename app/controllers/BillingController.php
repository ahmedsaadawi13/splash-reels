<?php
// FILE: /app/controllers/BillingController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Plan.php';
require_once __DIR__ . '/../models/TenantSubscription.php';
require_once __DIR__ . '/../models/TenantUsage.php';
require_once __DIR__ . '/../models/TenantApiKey.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';

class BillingController extends Controller {

    public function index() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        $tenantId = $this->getTenantId();

        // Get current plan and subscription
        $subscriptionModel = new TenantSubscription();
        $subscription = $subscriptionModel->getActiveSubscription($tenantId);

        $db = Database::getInstance();
        $plan = null;

        if ($subscription) {
            $plan = $db->fetchOne("SELECT * FROM plans WHERE id = ?", array($subscription['plan_id']));
        }

        // Get usage
        $usage = UsageHelper::getCurrentUsage($tenantId);
        $limits = UsageHelper::getPlanLimits($tenantId);

        // Get usage history (last 6 months)
        $usageModel = new TenantUsage();
        $usageHistory = $usageModel->getByTenant($tenantId, 6);

        // Get API keys
        $apiKeyModel = new TenantApiKey();
        $apiKeys = $apiKeyModel->getByTenant($tenantId);

        // Calculate percentages
        $minutesPercentage = UsageHelper::getUsagePercentage($usage['total_input_minutes'], $limits['max_minutes_input_per_month']);
        $exportsPercentage = UsageHelper::getUsagePercentage($usage['total_exported_clips'], $limits['max_exports_per_month']);
        $storagePercentage = UsageHelper::getUsagePercentage($usage['total_storage_mb'], $limits['storage_limit_mb']);

        return $this->view('billing/index', array(
            'subscription' => $subscription,
            'plan' => $plan,
            'usage' => $usage,
            'limits' => $limits,
            'usageHistory' => $usageHistory,
            'apiKeys' => $apiKeys,
            'minutesPercentage' => $minutesPercentage,
            'exportsPercentage' => $exportsPercentage,
            'storagePercentage' => $storagePercentage
        ));
    }

    public function plans() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        $planModel = new Plan();
        $plans = $planModel->getActive();

        $tenantId = $this->getTenantId();
        $subscriptionModel = new TenantSubscription();
        $currentSubscription = $subscriptionModel->getActiveSubscription($tenantId);

        return $this->view('billing/plans', array(
            'plans' => $plans,
            'currentSubscription' => $currentSubscription
        ));
    }

    public function createApiKey() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/billing');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $label = Request::post('label', 'API Key');

        $apiKeyModel = new TenantApiKey();
        $apiKeyId = $apiKeyModel->createApiKey($tenantId, $label);

        Auth::logActivity($tenantId, $this->getUserId(), 'create_api_key', 'tenant_api_key', $apiKeyId);

        Session::setFlash('success', 'API key created successfully');
        return $this->redirect('/billing');
    }

    public function revokeApiKey($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/billing');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $apiKeyModel = new TenantApiKey();

        $apiKey = $apiKeyModel->find($id);

        if (!$apiKey || $apiKey['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'API key not found');
            return $this->redirect('/billing');
        }

        $apiKeyModel->update($id, array('is_active' => 0));

        Auth::logActivity($tenantId, $this->getUserId(), 'revoke_api_key', 'tenant_api_key', $id);

        Session::setFlash('success', 'API key revoked successfully');
        return $this->redirect('/billing');
    }
}
