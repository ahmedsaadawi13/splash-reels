<?php
// FILE: /app/helpers/ScriptHelper.php

class ScriptHelper {

    public static function generateScript($topic, $tone = 'professional', $language = 'en', $targetDuration = 60) {
        // Simulated AI script generation
        // In production, this would call an AI API (OpenAI, Claude, etc.)

        $hooks = array(
            'Did you know that...',
            'Here\'s something incredible...',
            'Wait until you hear this...',
            'This will blow your mind...',
            'Pay attention to this...'
        );

        $closings = array(
            'Thanks for watching! Like and subscribe for more.',
            'Let me know your thoughts in the comments below.',
            'Drop a comment if you found this helpful!',
            'Hit that subscribe button for more content like this.',
            'Share this with someone who needs to see it!'
        );

        $hook = $hooks[array_rand($hooks)];
        $closing = $closings[array_rand($closings)];

        // Generate body paragraphs based on topic
        $wordCount = (int) ($targetDuration * 2.5); // Roughly 150 words per minute
        $paragraphCount = max(2, (int) ($wordCount / 50));

        $script = $hook . "\n\n";
        $script .= "Today we're talking about " . $topic . ".\n\n";

        for ($i = 0; $i < $paragraphCount; $i++) {
            $script .= self::generateParagraph($topic, $tone) . "\n\n";
        }

        $script .= $closing;

        return array(
            'script' => $script,
            'word_count' => str_word_count($script),
            'estimated_duration' => self::estimateDuration($script)
        );
    }

    private static function generateParagraph($topic, $tone) {
        $templates = array(
            "This is a key point about {$topic}. Understanding this concept can really help you improve your approach and get better results.",
            "When it comes to {$topic}, many people overlook this important aspect. Let me break it down for you in a simple way.",
            "Here's what most people don't realize about {$topic}. This insight has helped countless individuals achieve their goals.",
            "Let's dive deeper into {$topic}. The fundamentals are actually quite straightforward once you understand the basics.",
            "One of the most powerful strategies for {$topic} is this approach. It's proven to work time and time again."
        );

        return $templates[array_rand($templates)];
    }

    public static function estimateDuration($text) {
        $wordCount = str_word_count($text);
        $wordsPerMinute = 150;
        return ceil(($wordCount / $wordsPerMinute) * 60); // Return in seconds
    }

    public static function breakIntoSegments($text, $segmentLengthSeconds = 5) {
        $words = explode(' ', $text);
        $wordsPerSecond = 150 / 60; // Roughly 2.5 words per second

        $segments = array();
        $currentSegment = array();
        $currentDuration = 0;
        $totalDuration = 0;

        foreach ($words as $word) {
            $currentSegment[] = $word;
            $wordDuration = 1 / $wordsPerSecond;
            $currentDuration += $wordDuration;

            if ($currentDuration >= $segmentLengthSeconds) {
                $segments[] = array(
                    'start' => $totalDuration,
                    'end' => $totalDuration + $currentDuration,
                    'text' => implode(' ', $currentSegment)
                );

                $totalDuration += $currentDuration;
                $currentSegment = array();
                $currentDuration = 0;
            }
        }

        // Add remaining words
        if (!empty($currentSegment)) {
            $segments[] = array(
                'start' => $totalDuration,
                'end' => $totalDuration + $currentDuration,
                'text' => implode(' ', $currentSegment)
            );
        }

        return $segments;
    }
}
