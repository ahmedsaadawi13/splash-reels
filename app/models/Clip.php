<?php
// FILE: /app/models/Clip.php

class Clip extends Model {
    protected $table = 'clips';
    protected $fillable = array('tenant_id', 'project_id', 'media_file_id', 'script_source_id', 'clip_template_id', 'title', 'description', 'start_seconds', 'end_seconds', 'duration_seconds', 'status', 'preview_thumbnail_path', 'output_file_path', 'platform_hint', 'created_by_user_id');

    public function getByProject($projectId) {
        return $this->where(array('project_id' => $projectId), 'created_at DESC');
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getReady($projectId) {
        return $this->where(array('project_id' => $projectId, 'status' => 'ready'), 'created_at DESC');
    }

    public function getByMediaFile($mediaFileId) {
        return $this->where(array('media_file_id' => $mediaFileId), 'created_at DESC');
    }
}
