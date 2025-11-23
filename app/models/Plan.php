<?php
// FILE: /app/models/Plan.php

class Plan extends Model {
    protected $table = 'plans';
    protected $fillable = array('name', 'slug', 'price_monthly', 'price_yearly', 'max_projects', 'max_minutes_input_per_month', 'max_exports_per_month', 'max_users', 'storage_limit_mb', 'features_json', 'is_active');

    public function getActive() {
        return $this->where(array('is_active' => 1), 'price_monthly ASC');
    }

    public function findBySlug($slug) {
        return $this->findWhere(array('slug' => $slug));
    }
}
