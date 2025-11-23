<?php
// FILE: /app/models/ClipTemplate.php

class ClipTemplate extends Model {
    protected $table = 'clip_templates';
    protected $fillable = array('tenant_id', 'name', 'description', 'aspect_ratio', 'safe_zone_json', 'font_family', 'font_color', 'background_color', 'overlays_json', 'is_default');

    public function getGlobalTemplates() {
        $sql = "SELECT * FROM clip_templates WHERE tenant_id IS NULL ORDER BY is_default DESC, name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getAllAvailable($tenantId) {
        $sql = "SELECT * FROM clip_templates
                WHERE tenant_id IS NULL OR tenant_id = ?
                ORDER BY is_default DESC, name ASC";
        return $this->db->fetchAll($sql, array($tenantId));
    }
}
