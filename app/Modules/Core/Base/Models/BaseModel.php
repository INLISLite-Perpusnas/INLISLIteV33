<?php

namespace Base\Models;

class BaseModel extends \CodeIgniter\Model
{
    protected $DBGroup	= 'default';
	protected $table = 'table';
    protected $primaryKey = 'id'; 
    protected $returnType = 'object';
    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function __construct($table = null, $alias = null, $primaryKey = null)
    {
        parent::__construct();

        if ($table) {
            $this->setTable($table, $alias);
        }

        if ($primaryKey) {
            $this->setPrimaryKey($primaryKey);
        }
    }

    public function setTable($table, $alias = null)
    {
        $this->table = $table;
        if ($alias) {
            $this->table .= ' ' . $alias;
        }

        return $this;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }
}
