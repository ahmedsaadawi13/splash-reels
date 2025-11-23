<?php
// FILE: /app/helpers/VideoPipelineHelper.php

class VideoPipelineHelper {

    public static function generateTranscript($mediaFileId) {
        // Simulated AI transcription (in production, use Whisper, AssemblyAI, etc.)

        $db = Database::getInstance();

        // Get media file info
        $mediaFile = $db->fetchOne("SELECT * FROM media_files WHERE id = ?", array($mediaFileId));

        if (!$mediaFile) {
            return array('success' => false, 'message' => 'Media file not found');
        }

        // Check if transcript already exists
        $existing = $db->fetchOne("SELECT id FROM transcripts WHERE media_file_id = ?", array($mediaFileId));

        if ($existing) {
            return array('success' => true, 'message' => 'Transcript already exists', 'transcript_id' => $existing['id']);
        }

        // Generate simulated transcript
        $transcriptText = self::simulateTranscript($mediaFile['duration_seconds']);

        // Insert transcript
        $sql = "INSERT INTO transcripts (tenant_id, media_file_id, transcript_text, language)
                VALUES (?, ?, ?, ?)";

        $db->execute($sql, array(
            $mediaFile['tenant_id'],
            $mediaFileId,
            $transcriptText,
            'en'
        ));

        $transcriptId = $db->lastInsertId();

        return array('success' => true, 'transcript_id' => $transcriptId);
    }

    public static function generateClipSuggestions($mediaFileId) {
        // Simulated AI highlight detection

        $db = Database::getInstance();

        // Get media file info
        $mediaFile = $db->fetchOne("SELECT * FROM media_files WHERE id = ?", array($mediaFileId));

        if (!$mediaFile) {
            return array('success' => false, 'message' => 'Media file not found');
        }

        // Ensure transcript exists
        $transcriptResult = self::generateTranscript($mediaFileId);

        // Generate 3-5 clip suggestions
        $suggestionsCount = rand(3, 5);
        $duration = $mediaFile['duration_seconds'];
        $suggestions = array();

        $titles = array(
            'Key Insight Revealed',
            'Mind-Blowing Moment',
            'Expert Tips Shared',
            'Game-Changing Strategy',
            'Must-See Highlight',
            'Pro Tips & Tricks',
            'Shocking Discovery',
            'Valuable Lesson',
            'Quick Win Strategy',
            'Power Move Explained'
        );

        $captions = array(
            'This changed everything! 🚀',
            'You need to see this 👀',
            'Game changer right here! 💡',
            'Watch until the end! ⚡',
            'This is pure gold! 🏆',
            'Mind = Blown 🤯',
            'Save this for later! 📌',
            'Share this with your team! 👥'
        );

        for ($i = 0; $i < $suggestionsCount; $i++) {
            $clipDuration = rand(15, 60); // 15-60 second clips
            $maxStart = max(0, $duration - $clipDuration - 10);
            $startSeconds = rand(0, $maxStart);
            $endSeconds = $startSeconds + $clipDuration;

            $sql = "INSERT INTO clip_suggestions
                    (tenant_id, media_file_id, start_seconds, end_seconds, suggested_title, suggested_caption_text, confidence_score, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $db->execute($sql, array(
                $mediaFile['tenant_id'],
                $mediaFileId,
                $startSeconds,
                $endSeconds,
                $titles[array_rand($titles)],
                $captions[array_rand($captions)],
                number_format(rand(75, 95) / 100, 2),
                'suggested'
            ));

            $suggestions[] = $db->lastInsertId();
        }

        // Update media file status
        $db->execute("UPDATE media_files SET status = 'ready' WHERE id = ?", array($mediaFileId));

        return array('success' => true, 'suggestions' => $suggestions, 'count' => count($suggestions));
    }

    public static function renderClip($clipId) {
        // Simulated clip rendering (in production, use FFmpeg)

        $db = Database::getInstance();

        // Get clip info
        $clip = $db->fetchOne("SELECT * FROM clips WHERE id = ?", array($clipId));

        if (!$clip) {
            return array('success' => false, 'message' => 'Clip not found');
        }

        // Update status to processing
        $db->execute("UPDATE clips SET status = 'processing' WHERE id = ?", array($clipId));

        // Simulate processing delay
        sleep(1);

        // Generate output paths
        $outputFilePath = "/storage/uploads/clips/{$clip['tenant_id']}/clip_{$clipId}.mp4";
        $thumbnailPath = "/storage/uploads/thumbnails/{$clip['tenant_id']}/clip_{$clipId}_thumb.jpg";

        // Ensure directories exist
        $clipDir = __DIR__ . "/../../storage/uploads/clips/{$clip['tenant_id']}";
        $thumbDir = __DIR__ . "/../../storage/uploads/thumbnails/{$clip['tenant_id']}";

        if (!is_dir($clipDir)) {
            mkdir($clipDir, 0755, true);
        }
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        // Create dummy files (in production, FFmpeg would create real files)
        file_put_contents(__DIR__ . '/../..' . $outputFilePath, '');
        file_put_contents(__DIR__ . '/../..' . $thumbnailPath, '');

        // Update clip with output paths
        $db->execute(
            "UPDATE clips SET status = 'ready', output_file_path = ?, preview_thumbnail_path = ? WHERE id = ?",
            array($outputFilePath, $thumbnailPath, $clipId)
        );

        return array('success' => true, 'clip_id' => $clipId, 'output_path' => $outputFilePath);
    }

    public static function exportClip($clipId, $format = 'mp4', $resolution = '1080x1920') {
        // Simulated export (in production, use FFmpeg for format conversion)

        $db = Database::getInstance();

        // Get clip info
        $clip = $db->fetchOne("SELECT * FROM clips WHERE id = ?", array($clipId));

        if (!$clip) {
            return array('success' => false, 'message' => 'Clip not found');
        }

        if ($clip['status'] !== 'ready') {
            return array('success' => false, 'message' => 'Clip is not ready for export');
        }

        // Create export record
        $sql = "INSERT INTO exports (tenant_id, clip_id, format, resolution, status)
                VALUES (?, ?, ?, ?, ?)";

        $db->execute($sql, array(
            $clip['tenant_id'],
            $clipId,
            $format,
            $resolution,
            'processing'
        ));

        $exportId = $db->lastInsertId();

        // Simulate processing
        sleep(1);

        // Use clip's output file as download URL (in production, might be different)
        $downloadUrl = $clip['output_file_path'];

        // Update export status
        $db->execute(
            "UPDATE exports SET status = 'ready', download_url = ? WHERE id = ?",
            array($downloadUrl, $exportId)
        );

        // Update usage
        UsageHelper::incrementExports($clip['tenant_id']);

        return array('success' => true, 'export_id' => $exportId, 'download_url' => $downloadUrl);
    }

    private static function simulateTranscript($durationSeconds) {
        $segments = array(
            '[0:00] Welcome everyone to this amazing video.',
            '[0:05] Today we are going to cover some incredible insights.',
            '[0:15] Let me start by introducing the main concept.',
            '[0:30] This is where things get really interesting.',
            '[0:45] Pay close attention to this next part.',
            '[1:00] Here are the key takeaways you need to remember.',
            '[1:30] Now let\'s dive into the practical applications.',
            '[2:00] As you can see, the results speak for themselves.',
            '[2:30] Many people have already benefited from this approach.',
            '[3:00] Let me show you a real-world example.',
            '[3:30] The data clearly demonstrates the effectiveness.',
            '[4:00] This strategy has proven successful time and again.',
            '[4:30] Here\'s what you should do next.',
            '[5:00] Remember to implement these steps carefully.',
            '[5:30] Thanks for watching, and see you in the next video!'
        );

        // Select segments based on duration
        $segmentsNeeded = min((int)($durationSeconds / 30), count($segments));
        $selectedSegments = array_slice($segments, 0, $segmentsNeeded);

        return implode(' ', $selectedSegments);
    }
}
