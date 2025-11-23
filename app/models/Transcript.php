<?php
// FILE: /app/models/Transcript.php

class Transcript extends Model {
    protected $table = 'transcripts';
    protected $fillable = array('tenant_id', 'media_file_id', 'transcript_text', 'language');

    public function getByMediaFile($mediaFileId) {
        return $this->findWhere(array('media_file_id' => $mediaFileId));
    }
}
