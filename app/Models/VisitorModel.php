<?php

namespace App\Models;

class VisitorModel extends BaseModel
{
    protected $table      = 'c_visitors';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id', 'ip_address', 'ip_country', 'ip_regionName', 'ip_city', 'ip_lat', 'ip_lon', 'ip_isp', 'timestamp', 'hits', 'created_by', 'updated_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;
}
