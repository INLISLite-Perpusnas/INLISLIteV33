<?php

namespace Template\Models;

class TemplateModel extends \Base\Models\BaseModel
{
    protected $DBGroup              = 'default';
    protected $table                  = 't_template';
    protected $primaryKey             = 'id';
    protected $returnType             = 'object';
    protected $useSoftDeletes         = false;
    protected $protectFields         = false;
    protected $useTimestamps         = true;
    protected $createdField          = 'created_at';
    protected $updatedField          = 'updated_at';
    protected $deletedField          = 'deleted_at';
    protected $validationRules        = [];
    protected $validationMessages     = [];
    protected $skipValidation         = true;
}
