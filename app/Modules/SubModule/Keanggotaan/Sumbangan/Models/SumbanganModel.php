<?php

namespace Sumbangan\Models;

class SumbanganModel extends \App\Models\BaseModel
{
    protected $DBGroup      = 'inlis';
    protected $table      = 'sumbangan';
    protected $primaryKey = 'ID';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    // protected $allowedFields = [
    //     'id', 'slug', 'name','t_member_id' , 'jumlah','description', 'sort',  'active', 'created_by', 'updated_by'
    // ];
    protected $useTimestamps = true;
    protected $createdField  = 'CreateDate';
    protected $updatedField  = 'UpdateDate';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
