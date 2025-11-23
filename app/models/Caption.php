<?php
// FILE: /app/models/Caption.php

class Caption extends Model {
    protected $table = 'captions';
    protected $fillable = array('tenant_id', 'clip_id', 'caption_json', 'style_json');

    public function getByClip($clipId) {
        return $this->findWhere(array('clip_id' => $clipId));
    }
}
