<?php
// FILE: /app/models/ActivityLog.php

class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $fillable = array('tenant_id', 'user_id', 'action', 'entity_type', 'entity_id', 'ip_address', 'user_agent', 'metadata_json');

    public function getByTenant($tenantId, $limit = 100) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC', $limit);
    }

    public function getByUser($userId, $limit = 100) {
        return $this->where(array('user_id' => $userId), 'created_at DESC', $limit);
    }
}
