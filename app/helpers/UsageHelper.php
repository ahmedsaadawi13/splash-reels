<?php
// FILE: /app/helpers/UsageHelper.php

class UsageHelper {

    public static function getCurrentUsage($tenantId) {
        $db = Database::getInstance();
        $currentMonth = date('Y-m');

        $sql = "SELECT * FROM tenant_usage WHERE tenant_id = ? AND month = ?";
        $usage = $db->fetchOne($sql, array($tenantId, $currentMonth));

        if (!$usage) {
            // Create usage record for current month
            $sql = "INSERT INTO tenant_usage (tenant_id, month) VALUES (?, ?)";
            $db->execute($sql, array($tenantId, $currentMonth));

            $usage = array(
                'tenant_id' => $tenantId,
                'month' => $currentMonth,
                'total_input_minutes' => 0,
                'total_exported_clips' => 0,
                'total_storage_mb' => 0,
                'api_calls_count' => 0,
                'projects_count' => 0
            );
        }

        return $usage;
    }

    public static function getPlanLimits($tenantId) {
        $db = Database::getInstance();

        $sql = "SELECT p.* FROM plans p
                INNER JOIN tenant_subscriptions ts ON p.id = ts.plan_id
                WHERE ts.tenant_id = ? AND ts.status = 'active'
                LIMIT 1";

        $plan = $db->fetchOne($sql, array($tenantId));

        if (!$plan) {
            // Return default limits if no active subscription
            return array(
                'max_projects' => 1,
                'max_minutes_input_per_month' => 10,
                'max_exports_per_month' => 5,
                'max_users' => 1,
                'storage_limit_mb' => 500
            );
        }

        return $plan;
    }

    public static function checkQuota($tenantId, $quotaType, $amount = 1) {
        $usage = self::getCurrentUsage($tenantId);
        $limits = self::getPlanLimits($tenantId);

        switch ($quotaType) {
            case 'input_minutes':
                $current = $usage['total_input_minutes'];
                $limit = $limits['max_minutes_input_per_month'];
                return array(
                    'allowed' => ($current + $amount) <= $limit,
                    'current' => $current,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $current)
                );

            case 'exports':
                $current = $usage['total_exported_clips'];
                $limit = $limits['max_exports_per_month'];
                return array(
                    'allowed' => ($current + $amount) <= $limit,
                    'current' => $current,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $current)
                );

            case 'storage':
                $current = $usage['total_storage_mb'];
                $limit = $limits['storage_limit_mb'];
                return array(
                    'allowed' => ($current + $amount) <= $limit,
                    'current' => $current,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $current)
                );

            case 'projects':
                $db = Database::getInstance();
                $current = $db->fetchColumn(
                    "SELECT COUNT(*) FROM projects WHERE tenant_id = ? AND status = 'active'",
                    array($tenantId)
                );
                $limit = $limits['max_projects'];
                return array(
                    'allowed' => ($current + $amount) <= $limit,
                    'current' => $current,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $current)
                );

            default:
                return array('allowed' => true);
        }
    }

    public static function incrementInputMinutes($tenantId, $seconds) {
        $minutes = TimecodeHelper::getTotalMinutes($seconds);
        $currentMonth = date('Y-m');
        $db = Database::getInstance();

        // Ensure usage record exists
        self::getCurrentUsage($tenantId);

        $sql = "UPDATE tenant_usage
                SET total_input_minutes = total_input_minutes + ?
                WHERE tenant_id = ? AND month = ?";

        $db->execute($sql, array($minutes, $tenantId, $currentMonth));
    }

    public static function incrementExports($tenantId) {
        $currentMonth = date('Y-m');
        $db = Database::getInstance();

        // Ensure usage record exists
        self::getCurrentUsage($tenantId);

        $sql = "UPDATE tenant_usage
                SET total_exported_clips = total_exported_clips + 1
                WHERE tenant_id = ? AND month = ?";

        $db->execute($sql, array($tenantId, $currentMonth));
    }

    public static function incrementStorage($tenantId, $sizeMB) {
        $currentMonth = date('Y-m');
        $db = Database::getInstance();

        // Ensure usage record exists
        self::getCurrentUsage($tenantId);

        $sql = "UPDATE tenant_usage
                SET total_storage_mb = total_storage_mb + ?
                WHERE tenant_id = ? AND month = ?";

        $db->execute($sql, array($sizeMB, $tenantId, $currentMonth));
    }

    public static function incrementApiCalls($tenantId) {
        $currentMonth = date('Y-m');
        $db = Database::getInstance();

        // Ensure usage record exists
        self::getCurrentUsage($tenantId);

        $sql = "UPDATE tenant_usage
                SET api_calls_count = api_calls_count + 1
                WHERE tenant_id = ? AND month = ?";

        $db->execute($sql, array($tenantId, $currentMonth));
    }

    public static function recalculateStorage($tenantId) {
        $db = Database::getInstance();

        $sql = "SELECT SUM(file_size_mb) as total FROM media_files WHERE tenant_id = ?";
        $result = $db->fetchOne($sql, array($tenantId));

        $totalStorageMB = $result['total'] ?: 0;

        $currentMonth = date('Y-m');

        // Ensure usage record exists
        self::getCurrentUsage($tenantId);

        $sql = "UPDATE tenant_usage SET total_storage_mb = ? WHERE tenant_id = ? AND month = ?";
        $db->execute($sql, array($totalStorageMB, $tenantId, $currentMonth));

        return $totalStorageMB;
    }

    public static function getUsagePercentage($current, $limit) {
        if ($limit == 0) {
            return 0;
        }
        return min(100, ($current / $limit) * 100);
    }
}
