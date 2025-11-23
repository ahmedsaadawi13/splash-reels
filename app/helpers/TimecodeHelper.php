<?php
// FILE: /app/helpers/TimecodeHelper.php

class TimecodeHelper {

    public static function secondsToTimecode($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    public static function timecodeToSeconds($timecode) {
        $parts = explode(':', $timecode);
        $parts = array_reverse($parts);

        $seconds = 0;
        $multiplier = 1;

        foreach ($parts as $part) {
            $seconds += (int) $part * $multiplier;
            $multiplier *= 60;
        }

        return $seconds;
    }

    public static function formatDuration($seconds) {
        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            if ($remainingSeconds > 0) {
                return $minutes . ' min ' . $remainingSeconds . ' sec';
            }
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return $hours . ' hr ' . $remainingMinutes . ' min';
        }

        return $hours . ' hr';
    }

    public static function getTotalMinutes($seconds) {
        return ceil($seconds / 60);
    }
}
