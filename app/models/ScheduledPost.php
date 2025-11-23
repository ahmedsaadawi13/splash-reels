<?php
// FILE: /app/models/ScheduledPost.php

class ScheduledPost extends Model {
    protected $table = 'scheduled_posts';
    protected $fillable = array('tenant_id', 'clip_id', 'platform', 'social_account_id', 'scheduled_at', 'status', 'posted_at', 'error_message');

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'scheduled_at DESC');
    }

    public function getScheduled($tenantId) {
        return $this->where(array('tenant_id' => $tenantId, 'status' => 'scheduled'), 'scheduled_at ASC');
    }
}
