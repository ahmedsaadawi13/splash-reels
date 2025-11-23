<?php
// FILE: /app/models/Project.php

class Project extends Model {
    protected $table = 'projects';
    protected $fillable = array('tenant_id', 'name', 'slug', 'description', 'status', 'created_by_user_id');

    public function createProject($data) {
        if (!isset($data['slug'])) {
            $data['slug'] = SlugHelper::generate($data['name']);
        }
        return $this->create($data);
    }

    public function getByTenant($tenantId, $status = null) {
        $where = array('tenant_id' => $tenantId);
        if ($status) {
            $where['status'] = $status;
        }
        return $this->where($where, 'created_at DESC');
    }

    public function getStats($projectId) {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM media_files WHERE project_id = ?) as media_count,
                    (SELECT COUNT(*) FROM clips WHERE project_id = ?) as clips_count,
                    (SELECT SUM(duration_seconds) FROM media_files WHERE project_id = ?) as total_duration
                ";
        return $this->db->fetchOne($sql, array($projectId, $projectId, $projectId));
    }
}
