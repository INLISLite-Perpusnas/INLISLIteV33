<?php

namespace Katalog\Models;

class FileModel extends \Base\Models\DataModel
{
    protected $DBGroup              = 'data';
    protected $table                  = 'catalogfiles';
    protected $primaryKey             = 'ID';
    protected $returnType             = 'object';
    protected $useSoftDeletes         = false;
    protected $protectFields         = false;
    protected $useTimestamps         = true;
    protected $createdField          = 'CreateDate';
    protected $updatedField          = 'UpdateDate';
    protected $validationRules        = [];
    protected $validationMessages     = [];
    protected $skipValidation         = true;
}
