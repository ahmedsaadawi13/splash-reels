<?php
// FILE: /public/index.php

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Load core classes
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/CSRF.php';
require_once __DIR__ . '/../app/core/Request.php';
require_once __DIR__ . '/../app/core/Response.php';
require_once __DIR__ . '/../app/core/View.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Router.php';

// Load helpers
require_once __DIR__ . '/../app/helpers/ValidationHelper.php';
require_once __DIR__ . '/../app/helpers/PaginatorHelper.php';
require_once __DIR__ . '/../app/helpers/FileUploadHelper.php';
require_once __DIR__ . '/../app/helpers/SlugHelper.php';
require_once __DIR__ . '/../app/helpers/TimecodeHelper.php';
require_once __DIR__ . '/../app/helpers/ScriptHelper.php';
require_once __DIR__ . '/../app/helpers/VideoPipelineHelper.php';
require_once __DIR__ . '/../app/helpers/UsageHelper.php';

// Start session
Session::start();

// Initialize router
$router = new Router();

// Load routes
require __DIR__ . '/../config/routes.php';

// Dispatch request
try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());

    if ($config['app']['debug']) {
        echo "<h1>Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
    } else {
        echo "<h1>500 - Server Error</h1>";
        echo "<p>An error occurred. Please try again later.</p>";
    }
}
