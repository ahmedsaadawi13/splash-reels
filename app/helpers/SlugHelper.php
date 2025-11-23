<?php
// FILE: /app/helpers/SlugHelper.php

class SlugHelper {

    public static function generate($text) {
        // Convert to lowercase
        $slug = strtolower($text);

        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');

        // Replace multiple hyphens with single hyphen
        $slug = preg_replace('/-+/', '-', $slug);

        return $slug;
    }

    public static function unique($text, $table, $field = 'slug', $id = null) {
        $slug = self::generate($text);
        $originalSlug = $slug;
        $counter = 1;

        $db = Database::getInstance();

        while (true) {
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$field} = ?";
            $params = array($slug);

            if ($id) {
                $sql .= " AND id != ?";
                $params[] = $id;
            }

            $count = $db->fetchColumn($sql, $params);

            if ($count == 0) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
