<?php
// FILE: /app/core/Model.php

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = array();
    protected $guarded = array('id', 'created_at', 'updated_at');

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all($where = array(), $orderBy = 'created_at DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($where)) {
            $conditions = array();
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql, array_values($where));
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchOne($sql, array($id));
    }

    public function findWhere($where, $orderBy = null) {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $conditions = array();
        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
        }
        $sql .= implode(' AND ', $conditions);

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        $sql .= " LIMIT 1";

        return $this->db->fetchOne($sql, array_values($where));
    }

    public function where($where, $orderBy = null, $limit = null) {
        return $this->all($where, $orderBy, $limit);
    }

    public function create($data) {
        $data = $this->filterFillable($data);

        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }
        if (isset($data['updated_at'])) {
            unset($data['updated_at']);
        }

        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $data = $this->filterFillable($data);

        if (isset($data['id'])) {
            unset($data['id']);
        }
        if (isset($data['created_at'])) {
            unset($data['created_at']);
        }

        $fields = array();
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = ?";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . "
                WHERE {$this->primaryKey} = ?";

        $values = array_values($data);
        $values[] = $id;

        return $this->db->execute($sql, $values);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, array($id));
    }

    public function count($where = array()) {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if (!empty($where)) {
            $conditions = array();
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        return (int) $this->db->fetchColumn($sql, array_values($where));
    }

    protected function filterFillable($data) {
        if (!empty($this->fillable)) {
            return array_intersect_key($data, array_flip($this->fillable));
        }

        if (!empty($this->guarded)) {
            return array_diff_key($data, array_flip($this->guarded));
        }

        return $data;
    }

    public function paginate($page = 1, $perPage = 20, $where = array(), $orderBy = 'created_at DESC') {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";

        if (!empty($where)) {
            $conditions = array();
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $items = $this->db->fetchAll($sql, array_values($where));
        $total = $this->count($where);

        return array(
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        );
    }
}
