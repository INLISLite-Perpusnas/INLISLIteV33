<?php

namespace JenisBahan\Models;

class JenisBahanModel extends \App\Models\BaseModel
{
    protected $DBGroup              = 'data';
    protected $table                  = 'worksheets';
    protected $primaryKey             = 'ID';
    protected $returnType             = 'object';
    protected $useSoftDeletes         = false;
    protected $protectFields         = false;
    protected $useTimestamps         = true;
    protected $createdField          = 'CreateDate';
    protected $updatedField          = 'UpdateDate';
    protected $deletedField          = 'DeleteDate';
    protected $validationRules        = [];
    protected $validationMessages     = [];
    protected $skipValidation         = true;
}
