<?php
// FILE: /app/models/ClipSuggestion.php

class ClipSuggestion extends Model {
    protected $table = 'clip_suggestions';
    protected $fillable = array('tenant_id', 'media_file_id', 'start_seconds', 'end_seconds', 'suggested_title', 'suggested_caption_text', 'confidence_score', 'status');

    public function getByMediaFile($mediaFileId, $status = null) {
        $where = array('media_file_id' => $mediaFileId);
        if ($status) {
            $where['status'] = $status;
        }
        return $this->where($where, 'confidence_score DESC');
    }

    public function getSuggested($mediaFileId) {
        return $this->where(array('media_file_id' => $mediaFileId, 'status' => 'suggested'), 'confidence_score DESC');
    }
}
