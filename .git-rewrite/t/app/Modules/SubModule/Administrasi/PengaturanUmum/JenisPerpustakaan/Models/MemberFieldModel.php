<?php

namespace JenisPerpustakaan\Models;

class MemberFieldModel extends \Base\Models\DataModel
{
    protected $DBGroup              = 'default';
    protected $table                  = 'member_fields';
    protected $primaryKey             = 'id';
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
