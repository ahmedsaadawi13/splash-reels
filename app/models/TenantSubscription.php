<?php
// FILE: /app/models/TenantSubscription.php

class TenantSubscription extends Model {
    protected $table = 'tenant_subscriptions';
    protected $fillable = array('tenant_id', 'plan_id', 'status', 'start_date', 'end_date', 'renewal_date');

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }

    public function getActiveSubscription($tenantId) {
        return $this->findWhere(array('tenant_id' => $tenantId, 'status' => 'active'));
    }
}
