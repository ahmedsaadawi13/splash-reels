<?php
// FILE: /app/models/Export.php

class Export extends Model {
    protected $table = 'exports';
    protected $fillable = array('tenant_id', 'clip_id', 'format', 'resolution', 'status', 'download_url');

    public function getByClip($clipId) {
        return $this->where(array('clip_id' => $clipId), 'created_at DESC');
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }
}
