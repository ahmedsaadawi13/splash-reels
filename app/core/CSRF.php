<?php
// FILE: /app/core/CSRF.php

class CSRF {

    public static function generateToken() {
        Session::start();
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function getToken() {
        return self::generateToken();
    }

    public static function verify($token) {
        Session::start();
        $sessionToken = Session::get('csrf_token');

        if (!$sessionToken || !$token) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function check() {
        $token = Request::post('csrf_token') ?: Request::header('X-CSRF-Token');

        if (!self::verify($token)) {
            Response::json(array(
                'success' => false,
                'message' => 'CSRF token validation failed'
            ), 403);
            exit;
        }
    }

    public static function field() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function meta() {
        $token = self::getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
