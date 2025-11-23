<?php
// FILE: /app/models/Script.php

class Script extends Model {
    protected $table = 'scripts';
    protected $fillable = array('tenant_id', 'project_id', 'title', 'script_text', 'language', 'target_duration_seconds', 'created_by_user_id');

    public function getByProject($projectId) {
        return $this->where(array('project_id' => $projectId), 'created_at DESC');
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }
}
