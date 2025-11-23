<?php
// FILE: /app/core/Controller.php

class Controller {

    protected function view($view, $data = array(), $layout = 'main') {
        return View::render($view, $data, $layout);
    }

    protected function json($data, $statusCode = 200) {
        return Response::json($data, $statusCode);
    }

    protected function redirect($url) {
        return Response::redirect($url);
    }

    protected function back() {
        return Response::back();
    }

    protected function requireAuth() {
        Auth::requireAuth();
    }

    protected function requireRole($roles) {
        Auth::requireRole($roles);
    }

    protected function validateCSRF() {
        if (Request::isPost()) {
            CSRF::check();
        }
    }

    protected function getTenantId() {
        return Auth::tenantId();
    }

    protected function getUserId() {
        return Auth::userId();
    }

    protected function getUser() {
        return Auth::user();
    }

    protected function hasRole($roles) {
        return Auth::hasRole($roles);
    }
}
