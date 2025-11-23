<?php
// FILE: /app/models/SocialAccount.php

class SocialAccount extends Model {
    protected $table = 'social_accounts';
    protected $fillable = array('tenant_id', 'platform', 'handle', 'access_token', 'status');

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getByPlatform($tenantId, $platform) {
        return $this->where(array('tenant_id' => $tenantId, 'platform' => $platform));
    }
}
