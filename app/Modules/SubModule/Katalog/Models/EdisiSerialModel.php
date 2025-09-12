<?php

namespace Katalog\Models;

class EdisiSerialModel extends \Base\Models\DataModel
{
  protected $DBGroup              = 'data';
  protected $table            = 'master_edisi_serial';
  protected $primaryKey       = 'ID';
  protected $returnType         = 'object';
  protected $useSoftDeletes     = false;
  protected $protectFields     = false;
  protected $useTimestamps     = true;
  protected $createdField      = 'CreateDate';
  protected $updatedField      = 'UpdateDate';
  protected $validationRules      = [];
  protected $validationMessages   = [];
  protected $skipValidation       = true;

  // Simple search method
  public function searchByKatalog($keyword)
  {
    return $this->where('Catalog_id', $keyword)   // exact match
      ->findAll(); // partial match
  }
}
