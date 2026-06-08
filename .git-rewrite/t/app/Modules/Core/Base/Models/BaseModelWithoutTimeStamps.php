<?php

namespace Base\Models;

class BaseModelWithoutTimeStamps extends \CodeIgniter\Model
{
    protected $DBGroup       = 'data';
	protected $table         = 'table';
    protected $primaryKey    = 'id'; 
    protected $returnType    = 'object';
    protected $protectFields = false;
    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

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
