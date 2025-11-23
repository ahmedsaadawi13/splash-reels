<?php
// FILE: /app/models/User.php

class User extends Model {
    protected $table = 'users';
    protected $fillable = array('tenant_id', 'email', 'password_hash', 'first_name', 'last_name', 'role', 'status');

    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->create($data);
    }

    public function updateUser($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        unset($data['password']);

        return $this->update($id, $data);
    }

    public function findByEmail($email) {
        return $this->findWhere(array('email' => $email));
    }

    public function getByTenant($tenantId) {
        return $this->where(array('tenant_id' => $tenantId), 'created_at DESC');
    }
}
