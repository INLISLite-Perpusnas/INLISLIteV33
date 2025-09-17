<?php

namespace Katalog\Models;

class SerialArticleFilesModel extends \Base\Models\DataModel
{
    protected $DBGroup              = 'default';
    protected $table                  = 'serial_articlefiles';
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

    public function getWithArticle($Catalog_id)
    {
        return $this->select('serial_articlefiles.*, serial_articles.title, serial_articles.EDISISERIAL')
                    ->join('serial_articles', 'serial_articles.id = serial_articlefiles.Articles_id')
                    ->orderBy('serial_articlefiles.UpdateDate', 'DESC')
                    ->where('serial_articles.Catalog_id', $Catalog_id)
                    ->findAll();
    }
}
