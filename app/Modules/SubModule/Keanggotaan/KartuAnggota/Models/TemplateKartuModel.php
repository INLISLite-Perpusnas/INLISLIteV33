<?php

namespace KartuAnggota\Models\Models;

class TemplateKartuModel extends \App\Models\BaseModel
{
    protected $DBGroup              = 'backend';
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
