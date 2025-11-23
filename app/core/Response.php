<?php
// FILE: /app/core/Response.php

class Response {

    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function redirect($url, $statusCode = 302) {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    public static function back() {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        self::redirect($referer);
    }

    public static function setStatusCode($code) {
        http_response_code($code);
    }

    public static function setHeader($key, $value) {
        header("$key: $value");
    }

    public static function notFound($message = 'Page not found') {
        self::setStatusCode(404);
        echo "<h1>404 - $message</h1>";
        exit;
    }

    public static function forbidden($message = 'Access denied') {
        self::setStatusCode(403);
        echo "<h1>403 - $message</h1>";
        exit;
    }

    public static function serverError($message = 'Internal server error') {
        self::setStatusCode(500);
        echo "<h1>500 - $message</h1>";
        exit;
    }
}
