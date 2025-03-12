<?php

namespace App\Models;
use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $table      = 'table';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['name', 'description'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function setTable($table)
    {
        $this->builder = $this->db->table($table);
    }

    public function get_single($where)
    {
        $this->builder->where($where);
        $query = $this->builder->get();
        return $query->getRow();
    }

    public function get_all($where, $by = 'id', $order = 'desc')
    {
        $this->builder->orderBy($by, $order);
        $this->builder->where($where);
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function is_exist($where)
    {
        return count($this->get_single($where)) > 0;
    }

    public function count($where)
    {
        return count($this->get_all($where));
    }

    public function find_all($by = 'id', $order = 'desc')
    {
        $query = $this->builder->orderBy($by, $order)->get();
        return $query->getResult();
    }

    public function row()
    {
        $query = $this->builder->get();
        return $query->getRow();
    }

    public function rowArray()
    {
        $query = $this->builder->get();
        return $query->getRowArray();
    }

    public function result(): array
    {
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function resultArray(): array
    {
        $query = $this->builder->get();
        return $query->getResultArray();
    }
    
    public function numRows(): int
    {
        $query = $this->builder->get();
        return $query->numRows();
    }
}
