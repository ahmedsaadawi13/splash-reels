<?php
// FILE: /app/models/TenantApiKey.php

class TenantApiKey extends Model {
    protected $table = 'tenant_api_keys';
    protected $fillable = array('tenant_id', 'api_key', 'label', 'is_active', 'rate_limit_per_minute', 'last_used_at');

    public function createApiKey($tenantId, $label) {
        $apiKey = 'sk_live_' . bin2hex(random_bytes(32));

        return $this->create(array(
            'tenant_id' => $tenantId,
            'api_key' => $apiKey,
            'label' => $label,
            'is_active' => 1,
            'rate_limit_per_minute' => 60
        ));
    }

    public function findByKey($apiKey) {
        return $this->findWhere(array('api_key' => $apiKey, 'is_active' => 1));
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function updateLastUsed($id) {
        $sql = "UPDATE tenant_api_keys SET last_used_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, array($id));
    }
}
