<?php
// FILE: /config/routes.php

// Authentication Routes
$router->get('/', function() {
    if (Auth::check()) {
        Response::redirect('/dashboard');
    } else {
        Response::redirect('/auth/login');
    }
});

$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/auth/forgot-password', 'AuthController@forgotPassword');
$router->post('/auth/forgot-password', 'AuthController@forgotPassword');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Projects
$router->get('/projects', 'ProjectController@index');
$router->get('/projects/create', 'ProjectController@create');
$router->post('/projects/create', 'ProjectController@create');
$router->get('/projects/{id}', 'ProjectController@view');
$router->get('/projects/{id}/edit', 'ProjectController@edit');
$router->post('/projects/{id}/edit', 'ProjectController@edit');
$router->post('/projects/{id}/delete', 'ProjectController@delete');

// Media
$router->get('/media', 'MediaController@index');
$router->get('/media/upload', 'MediaController@upload');
$router->post('/media/upload', 'MediaController@upload');
$router->get('/media/view/{id}', 'MediaController@view');
$router->post('/media/{id}/generate-clips', 'MediaController@generateClips');
$router->post('/media/{id}/delete', 'MediaController@delete');

// Clips
$router->get('/clips', 'ClipController@index');
$router->get('/clips/create', 'ClipController@create');
$router->post('/clips/create', 'ClipController@create');
$router->get('/clips/suggestions/{id}', 'ClipController@suggestions');
$router->post('/clips/accept-suggestion/{id}', 'ClipController@acceptSuggestion');
$router->get('/clips/edit/{id}', 'ClipController@edit');
$router->post('/clips/edit/{id}', 'ClipController@edit');
$router->post('/clips/{id}/render', 'ClipController@render');
$router->post('/clips/{id}/export', 'ClipController@export');
$router->post('/clips/{id}/delete', 'ClipController@delete');

// Brand Kits
$router->get('/brand-kits', 'BrandKitController@index');
$router->get('/brand-kits/create', 'BrandKitController@create');
$router->post('/brand-kits/create', 'BrandKitController@create');
$router->get('/brand-kits/{id}/edit', 'BrandKitController@edit');
$router->post('/brand-kits/{id}/edit', 'BrandKitController@edit');
$router->post('/brand-kits/{id}/delete', 'BrandKitController@delete');

// Billing & Usage
$router->get('/billing', 'BillingController@index');
$router->get('/billing/plans', 'BillingController@plans');
$router->post('/billing/api-key/create', 'BillingController@createApiKey');
$router->post('/billing/api-key/{id}/revoke', 'BillingController@revokeApiKey');

// Users
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/create', 'UserController@create');
$router->get('/users/{id}/edit', 'UserController@edit');
$router->post('/users/{id}/edit', 'UserController@edit');
$router->post('/users/{id}/delete', 'UserController@delete');

// Admin (Platform Admin)
$router->get('/admin/tenants', 'TenantController@index');
$router->get('/admin/tenants/{id}', 'TenantController@view');
$router->post('/admin/tenants/{id}/update-status', 'TenantController@updateStatus');

// API Routes
$router->post('/api/v1/projects/create', 'ApiController@createProject');
$router->post('/api/v1/media/create', 'ApiController@createMedia');
$router->post('/api/v1/media/{id}/generate-clips', 'ApiController@generateClips');
$router->get('/api/v1/projects/{id}/clips', 'ApiController@getProjectClips');
$router->get('/api/v1/clips/{id}', 'ApiController@getClip');
