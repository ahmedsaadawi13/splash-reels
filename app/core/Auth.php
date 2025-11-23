<?php
// FILE: /app/core/Auth.php

class Auth {

    public static function attempt($email, $password) {
        // Check brute force protection
        if (!self::checkLoginAttempts($email)) {
            return array('success' => false, 'message' => 'Too many login attempts. Please try again later.');
        }

        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1";
        $user = $db->fetchOne($sql, array($email));

        if (!$user) {
            self::recordFailedAttempt($email);
            return array('success' => false, 'message' => 'Invalid credentials.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            self::recordFailedAttempt($email);
            return array('success' => false, 'message' => 'Invalid credentials.');
        }

        // Clear failed attempts
        self::clearFailedAttempts($email);

        // Update last login
        $db->execute("UPDATE users SET last_login_at = NOW() WHERE id = ?", array($user['id']));

        // Set session
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('tenant_id', $user['tenant_id']);
        Session::set('role', $user['role']);
        Session::set('email', $user['email']);
        Session::set('name', $user['first_name'] . ' ' . $user['last_name']);

        // Log activity
        self::logActivity($user['tenant_id'], $user['id'], 'login', 'user', $user['id']);

        return array('success' => true, 'user' => $user);
    }

    public static function check() {
        return Session::has('user_id');
    }

    public static function user() {
        if (!self::check()) {
            return null;
        }

        $userId = Session::get('user_id');
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        return $db->fetchOne($sql, array($userId));
    }

    public static function userId() {
        return Session::get('user_id');
    }

    public static function tenantId() {
        return Session::get('tenant_id');
    }

    public static function role() {
        return Session::get('role');
    }

    public static function logout() {
        Session::destroy();
    }

    public static function hasRole($roles) {
        if (!is_array($roles)) {
            $roles = array($roles);
        }
        return in_array(self::role(), $roles);
    }

    public static function requireAuth() {
        if (!self::check()) {
            Response::redirect('/auth/login');
            exit;
        }
    }

    public static function requireRole($roles) {
        self::requireAuth();
        if (!self::hasRole($roles)) {
            Response::redirect('/dashboard');
            exit;
        }
    }

    private static function checkLoginAttempts($email) {
        $db = Database::getInstance();
        $ip = self::getIpAddress();

        // Check last 15 minutes
        $sql = "SELECT COUNT(*) FROM login_attempts
                WHERE (email = ? OR ip_address = ?)
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        $count = $db->fetchColumn($sql, array($email, $ip));

        return $count < 5; // Max 5 attempts in 15 minutes
    }

    private static function recordFailedAttempt($email) {
        $db = Database::getInstance();
        $ip = self::getIpAddress();
        $sql = "INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)";
        $db->execute($sql, array($email, $ip));
    }

    private static function clearFailedAttempts($email) {
        $db = Database::getInstance();
        $ip = self::getIpAddress();
        $sql = "DELETE FROM login_attempts WHERE email = ? OR ip_address = ?";
        $db->execute($sql, array($email, $ip));
    }

    private static function getIpAddress() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function logActivity($tenantId, $userId, $action, $entityType = null, $entityId = null, $metadata = null) {
        $db = Database::getInstance();
        $ip = self::getIpAddress();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        $sql = "INSERT INTO activity_logs (tenant_id, user_id, action, entity_type, entity_id, ip_address, user_agent, metadata_json)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $metadataJson = $metadata ? json_encode($metadata) : null;
        $db->execute($sql, array($tenantId, $userId, $action, $entityType, $entityId, $ip, $userAgent, $metadataJson));
    }
}
