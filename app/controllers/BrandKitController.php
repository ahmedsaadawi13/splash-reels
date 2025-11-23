<?php
// FILE: /app/controllers/BrandKitController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/BrandKit.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/FileUploadHelper.php';

class BrandKitController extends Controller {

    public function index() {
        $this->requireAuth();

        $tenantId = $this->getTenantId();
        $brandKitModel = new BrandKit();

        $brandKits = $brandKitModel->getByTenant($tenantId);

        return $this->view('brand-kits/index', array(
            'brandKits' => $brandKits
        ));
    }

    public function create() {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'name' => 'required|min:2|max:255'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $tenantId = $this->getTenantId();
            $brandKitModel = new BrandKit();

            $data = array(
                'tenant_id' => $tenantId,
                'name' => Request::post('name'),
                'primary_color' => Request::post('primary_color', '#000000'),
                'secondary_color' => Request::post('secondary_color', '#FFFFFF'),
                'accent_color' => Request::post('accent_color', '#FF0000'),
                'font_family' => Request::post('font_family', 'Arial')
            );

            // Handle logo upload
            $logoFile = Request::file('logo');
            if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUploadHelper::uploadImage($logoFile, $tenantId, 'logos');
                if ($uploadResult['success']) {
                    $data['logo_path'] = $uploadResult['path'];
                }
            }

            $brandKitId = $brandKitModel->create($data);

            Auth::logActivity($tenantId, $this->getUserId(), 'create_brand_kit', 'brand_kit', $brandKitId);

            Session::setFlash('success', 'Brand kit created successfully');
            return $this->redirect('/brand-kits');
        }

        return $this->view('brand-kits/create');
    }

    public function edit($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin', 'editor'));

        $tenantId = $this->getTenantId();
        $brandKitModel = new BrandKit();

        $brandKit = $brandKitModel->find($id);

        if (!$brandKit || $brandKit['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Brand kit not found');
            return $this->redirect('/brand-kits');
        }

        if (Request::isPost()) {
            $this->validateCSRF();

            $errors = ValidationHelper::validateData($_POST, array(
                'name' => 'required|min:2|max:255'
            ));

            if (!empty($errors)) {
                Session::setFlash('error', 'Please check your input');
                return $this->back();
            }

            $data = array(
                'name' => Request::post('name'),
                'primary_color' => Request::post('primary_color'),
                'secondary_color' => Request::post('secondary_color'),
                'accent_color' => Request::post('accent_color'),
                'font_family' => Request::post('font_family')
            );

            // Handle logo upload
            $logoFile = Request::file('logo');
            if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
                $uploadResult = FileUploadHelper::uploadImage($logoFile, $tenantId, 'logos');
                if ($uploadResult['success']) {
                    $data['logo_path'] = $uploadResult['path'];
                }
            }

            $brandKitModel->update($id, $data);

            Auth::logActivity($tenantId, $this->getUserId(), 'update_brand_kit', 'brand_kit', $id);

            Session::setFlash('success', 'Brand kit updated successfully');
            return $this->redirect('/brand-kits');
        }

        return $this->view('brand-kits/edit', array('brandKit' => $brandKit));
    }

    public function delete($id) {
        $this->requireAuth();
        $this->requireRole(array('tenant_admin'));

        if (!Request::isPost()) {
            return $this->redirect('/brand-kits');
        }

        $this->validateCSRF();

        $tenantId = $this->getTenantId();
        $brandKitModel = new BrandKit();

        $brandKit = $brandKitModel->find($id);

        if (!$brandKit || $brandKit['tenant_id'] != $tenantId) {
            Session::setFlash('error', 'Brand kit not found');
            return $this->redirect('/brand-kits');
        }

        $brandKitModel->delete($id);

        Auth::logActivity($tenantId, $this->getUserId(), 'delete_brand_kit', 'brand_kit', $id);

        Session::setFlash('success', 'Brand kit deleted successfully');
        return $this->redirect('/brand-kits');
    }
}
