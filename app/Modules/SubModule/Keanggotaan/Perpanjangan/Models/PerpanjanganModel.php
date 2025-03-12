<?php

namespace Perpanjangan\Models;

class PerpanjanganModel extends \App\Models\BaseModel
{
    protected $DBGroup    = 'inlis';
    protected $table      = 'member_perpanjangan';
    protected $primaryKey = 'ID';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields  = false;
    protected $useTimestamps = true;
    protected $createdField  = 'CreateDate';
    protected $updatedField  = 'UpdateDate';
    // protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
