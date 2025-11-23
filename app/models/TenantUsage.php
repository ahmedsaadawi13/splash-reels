<?php
// FILE: /app/models/TenantUsage.php

class TenantUsage extends Model {
    protected $table = 'tenant_usage';
    protected $fillable = array('tenant_id', 'month', 'total_input_minutes', 'total_exported_clips', 'total_storage_mb', 'api_calls_count', 'projects_count');

    public function getCurrentMonth($tenantId) {
        $currentMonth = date('Y-m');
        return $this->findWhere(array('tenant_id' => $tenantId, 'month' => $currentMonth));
    }

    public function getByTenant($tenantId, $limit = 12) {
        return $this->where(array('tenant_id' => $tenantId), 'month DESC', $limit);
    }
}
