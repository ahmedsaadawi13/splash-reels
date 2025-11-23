<?php
// FILE: /app/controllers/DashboardController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/MediaFile.php';
require_once __DIR__ . '/../models/Clip.php';
require_once __DIR__ . '/../helpers/UsageHelper.php';

class DashboardController extends Controller {

    public function index() {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $db = Database::getInstance();

        // Get usage data
        $usage = UsageHelper::getCurrentUsage($tenantId);
        $limits = UsageHelper::getPlanLimits($tenantId);

        // Get counts
        $projectsCount = $db->fetchColumn(
            "SELECT COUNT(*) FROM projects WHERE tenant_id = ? AND status = 'active'",
            array($tenantId)
        );

        $clipsCount = $db->fetchColumn(
            "SELECT COUNT(*) FROM clips WHERE tenant_id = ?",
            array($tenantId)
        );

        $clipsReadyCount = $db->fetchColumn(
            "SELECT COUNT(*) FROM clips WHERE tenant_id = ? AND status = 'ready'",
            array($tenantId)
        );

        $mediaCount = $db->fetchColumn(
            "SELECT COUNT(*) FROM media_files WHERE tenant_id = ?",
            array($tenantId)
        );

        // Get recent projects
        $projectModel = new Project();
        $recentProjects = $projectModel->getByTenant($tenantId);
        if (count($recentProjects) > 5) {
            $recentProjects = array_slice($recentProjects, 0, 5);
        }

        // Get recent clips
        $clipModel = new Clip();
        $recentClips = $clipModel->getByTenant($tenantId);
        if (count($recentClips) > 5) {
            $recentClips = array_slice($recentClips, 0, 5);
        }

        // Calculate quota percentages
        $minutesPercentage = UsageHelper::getUsagePercentage(
            $usage['total_input_minutes'],
            $limits['max_minutes_input_per_month']
        );

        $exportsPercentage = UsageHelper::getUsagePercentage(
            $usage['total_exported_clips'],
            $limits['max_exports_per_month']
        );

        $storagePercentage = UsageHelper::getUsagePercentage(
            $usage['total_storage_mb'],
            $limits['storage_limit_mb']
        );

        return $this->view('dashboard/index', array(
            'projectsCount' => $projectsCount,
            'clipsCount' => $clipsCount,
            'clipsReadyCount' => $clipsReadyCount,
            'mediaCount' => $mediaCount,
            'recentProjects' => $recentProjects,
            'recentClips' => $recentClips,
            'usage' => $usage,
            'limits' => $limits,
            'minutesPercentage' => $minutesPercentage,
            'exportsPercentage' => $exportsPercentage,
            'storagePercentage' => $storagePercentage
        ));
    }
}
