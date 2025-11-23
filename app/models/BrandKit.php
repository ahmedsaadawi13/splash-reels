<?php
// FILE: /app/models/BrandKit.php

class BrandKit extends Model {
    protected $table = 'brand_kits';
    protected $fillable = array('tenant_id', 'name', 'primary_color', 'secondary_color', 'accent_color', 'font_family', 'logo_path', 'outro_template_json');

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getDefault($tenantId) {
        return $this->findWhere(array('tenant_id' => $tenantId), 'created_at ASC');
    }
}
