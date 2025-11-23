<?php
// FILE: /app/models/Tenant.php

class Tenant extends Model {
    protected $table = 'tenants';
    protected $fillable = array('name', 'slug', 'domain', 'status', 'settings_json');

    public function createTenant($data) {
        if (!isset($data['slug'])) {
            $data['slug'] = SlugHelper::unique($data['name'], 'tenants', 'slug');
        }
        return $this->create($data);
    }

    public function findBySlug($slug) {
        return $this->findWhere(array('slug' => $slug));
    }

    public function getActiveSubscription($tenantId) {
        $sql = "SELECT ts.*, p.* FROM tenant_subscriptions ts
                INNER JOIN plans p ON ts.plan_id = p.id
                WHERE ts.tenant_id = ? AND ts.status = 'active'
                LIMIT 1";
        return $this->db->fetchOne($sql, array($tenantId));
    }
}
