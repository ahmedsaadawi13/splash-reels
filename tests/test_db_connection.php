<?php
// FILE: /tests/test_db_connection.php

require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance();
    $result = $db->fetchOne("SELECT 1 as test");

    if ($result && $result['test'] == 1) {
        echo "OK - Database connection successful\n";
    } else {
        echo "FAIL - Unexpected result from database\n";
    }
} catch (Exception $e) {
    echo "FAIL - Database connection failed: " . $e->getMessage() . "\n";
}
