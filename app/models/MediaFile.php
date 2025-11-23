<?php
// FILE: /app/models/MediaFile.php

class MediaFile extends Model {
    protected $table = 'media_files';
    protected $fillable = array('tenant_id', 'project_id', 'title', 'description', 'source_type', 'source_url', 'file_path', 'duration_seconds', 'resolution', 'aspect_ratio', 'file_size_mb', 'status', 'created_by_user_id');

    public function getByProject($projectId) {
        return $this->where(array('project_id' => $projectId), 'created_at DESC');
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getReady($projectId) {
        return $this->where(array('project_id' => $projectId, 'status' => 'ready'), 'created_at DESC');
    }
}
