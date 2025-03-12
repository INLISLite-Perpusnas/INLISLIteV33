<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends BaseModel
{
    protected $table      = 'c_logs';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['message', 'controller', 'operation', 'ref_table', 'ref_id', 'description', 'created_by', 'updated_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    public function get_individu($from_date = null, $to_date = null, $items = null, $page = null)
    {
        $this->builder = $this->db->table('c_logs as l');
        $this->builder->select('l.*, u.first_name, u.last_name, u.username, u.email, u.unit, u.company');
        $this->builder->join('c_users u', 'u.id = l.created_by', 'left');

        if (!empty($from_date) && !empty($to_date)) {
            $this->builder->where('created_at >=', $from_date);
            $this->builder->where('created_at <=', $to_date);
        }

        $this->builder->orderBy('l.id', 'desc');
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function get_individu_count($from_date = null, $to_date = null)
    {
        if (!empty($from_date) && !empty($to_date)) {
            $this->builder->where('created_at >=', $from_date);
            $this->builder->where('created_at <=', $to_date);
        }

        $query = $this->builder->get();
        return count($query->getResult());
    }
}
