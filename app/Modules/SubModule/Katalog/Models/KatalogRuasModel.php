<?php

namespace Katalog\Models;

class KatalogRuasModel extends \Base\Models\DataModel
{
    protected $DBGroup              = 'data';
    protected $table                  = 'catalog_ruas';
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

    function __construct()
    {
        parent::__construct();
    }

    public function Update_catalog_ruas($data)
    {
        $id = array_column($data, 'CatalogId')[0];

        $conn = db_connect('data');
        $db = $conn->table($this->table);
        $this->db->table($this->table)->where('CatalogId', $id)->delete();

        return $db->insertBatch($data);
    }

    public function Insert_catalog_ruas($data)
    {
        $this->db->table($this->table)->insertBatch($data);
    }
}
