<?php

namespace KeranjangAnggota\Models;

class KeranjangAnggotaModel extends \hamkamannan\adminigniter\Models\BaseModel
{
    protected $table      = 't_keranjanganggota';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id', 't_anggota_id','description', 'sort',  'active', 'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
