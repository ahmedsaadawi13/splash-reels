<?php
// FILE: /app/core/Request.php

class Request {

    public static function method() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public static function uri() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');
        return $uri;
    }

    public static function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public static function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public static function input($key = null, $default = null) {
        $method = self::method();

        if ($method === 'GET') {
            return self::get($key, $default);
        }

        if ($method === 'POST') {
            return self::post($key, $default);
        }

        // For PUT, DELETE, etc., parse php://input
        static $parsedInput = null;
        if ($parsedInput === null) {
            $rawInput = file_get_contents('php://input');
            parse_str($rawInput, $parsedInput);
        }

        if ($key === null) {
            return $parsedInput;
        }

        return isset($parsedInput[$key]) ? $parsedInput[$key] : $default;
    }

    public static function all() {
        return array_merge(self::get(), self::post());
    }

    public static function has($key) {
        return isset($_REQUEST[$key]);
    }

    public static function file($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function header($key) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public static function isPost() {
        return self::method() === 'POST';
    }

    public static function isGet() {
        return self::method() === 'GET';
    }

    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function ip() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public static function userAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public static function segment($index) {
        $uri = trim(self::uri(), '/');
        $segments = explode('/', $uri);
        return isset($segments[$index]) ? $segments[$index] : null;
    }

    public static function segments() {
        $uri = trim(self::uri(), '/');
        return empty($uri) ? array() : explode('/', $uri);
    }
}
