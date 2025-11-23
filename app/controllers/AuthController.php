<?php
// FILE: /app/controllers/AuthController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Tenant.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/SlugHelper.php';

class AuthController extends Controller {

    public function login() {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $email = Request::post('email');
            $password = Request::post('password');

            $errors = ValidationHelper::validateData($_POST, array(
                'email' => 'required|email',
                'password' => 'required'
            ));

            if (empty($errors)) {
                $result = Auth::attempt($email, $password);

                if ($result['success']) {
                    Session::setFlash('success', 'Welcome back!');
                    return $this->redirect('/dashboard');
                }

                Session::setFlash('error', $result['message']);
            } else {
                Session::setFlash('error', 'Please check your input');
            }

            return $this->back();
        }

        return $this->view('auth/login', array(), 'auth');
    }

    public function register() {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'company_name' => 'required|min:2',
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'email' => 'required|email',
                'password' => 'required|min:8'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                Session::set('form_data', $_POST);
                return $this->back();
            }

            $tenantModel = new Tenant();
            $userModel = new User();

            // Check if email exists
            if ($userModel->findByEmail(Request::post('email'))) {
                Session::setFlash('error', 'Email already registered');
                return $this->back();
            }

            try {
                // Create tenant
                $tenantId = $tenantModel->createTenant(array(
                    'name' => Request::post('company_name'),
                    'status' => 'active'
                ));

                // Create admin user
                $userId = $userModel->createUser(array(
                    'tenant_id' => $tenantId,
                    'email' => Request::post('email'),
                    'password' => Request::post('password'),
                    'first_name' => Request::post('first_name'),
                    'last_name' => Request::post('last_name'),
                    'role' => 'tenant_admin',
                    'status' => 'active'
                ));

                // Create free subscription
                $db = Database::getInstance();
                $freePlan = $db->fetchOne("SELECT id FROM plans WHERE slug = 'free' LIMIT 1");

                if ($freePlan) {
                    $db->execute(
                        "INSERT INTO tenant_subscriptions (tenant_id, plan_id, status, start_date, renewal_date)
                         VALUES (?, ?, 'trialing', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))",
                        array($tenantId, $freePlan['id'])
                    );
                }

                Session::setFlash('success', 'Registration successful! Please login.');
                return $this->redirect('/auth/login');

            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                Session::setFlash('error', 'Registration failed. Please try again.');
                return $this->back();
            }
        }

        return $this->view('auth/register', array(), 'auth');
    }

    public function logout() {
        Auth::logout();
        Session::setFlash('success', 'Logged out successfully');
        return $this->redirect('/auth/login');
    }

    public function forgotPassword() {
        if (Request::isPost()) {
            $this->validateCSRF();

            $email = Request::post('email');

            if (ValidationHelper::email($email)) {
                // In production, send email here
                error_log("Password reset requested for: $email");

                Session::setFlash('success', 'Password reset instructions sent to your email');
            } else {
                Session::setFlash('error', 'Invalid email address');
            }

            return $this->back();
        }

        return $this->view('auth/forgot-password', array(), 'auth');
    }
}
