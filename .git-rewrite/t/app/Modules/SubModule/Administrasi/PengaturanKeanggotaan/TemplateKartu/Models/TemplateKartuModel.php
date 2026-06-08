<?php

namespace TemplateKartu\Models;

class TemplateKartuModel extends \Base\Models\DataModel
{
    protected $DBGroup                = 'backend';
    protected $table                  = 't_template';
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
