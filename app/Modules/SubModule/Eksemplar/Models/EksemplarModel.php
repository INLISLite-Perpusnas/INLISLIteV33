<?php

namespace Eksemplar\Models;

class EksemplarModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'collections';
    protected $primaryKey 			= 'ID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    protected $deletedField  		= 'DeleteDate';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;

	function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all_bib()
    {
        $conn = \Config\Database::connect('data');
        $query = $conn->query("
        SELECT collections.`ID` AS ID, collections.`NomorBarcode` AS noBarcode,collections.`TanggalPengadaan` AS tglPengadaan,collections.`NoInduk` AS noInduk, 
        CONCAT('<b>',catalogs.`Title`,'</b>','<br /><br />',IFNULL(catalogs.`Publikasi`,''),'<br />',IFNULL(collections.`EDISISERIAL`,''),'<br />',IFNULL(worksheets.`Name`,'')) AS biblio
        FROM collections
        INNER JOIN catalogs ON catalogs.ID = collections.Catalog_id
        INNER JOIN worksheets ON worksheets.ID = catalogs.Worksheet_id
        ");

        $row = $query->getResult();
        return $row;
    }

    // get data by id
    function get_by_id($id)
    {
        $conn      = \Config\Database::connect('data');
        $db = $conn->table($this->table);
        return $db->where($this->primaryKey, $id)
            ->get()
            ->getRowArray();
    }
    // update data
    function update_action($id, $data)
    {
        $conn = \Config\Database::connect('data');
        $db   = $conn->table($this->table);

        return $db->where('id', $id)->update($data);
    }

    // delete data
    function delete_action($id)
    {
        $conn      = \Config\Database::connect('data');
        $db = $conn->table($this->table);

        return $db->where($this->table, $id)->delete();
    }


     public function getByBarcode($barcode)
    {
        return $this->select('
            collections.*,
            catalogs.Title,
            catalogs.Author,
            catalogs.Publisher,
            catalogs.ControlNumber
        ')
        ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left')
        ->where('collections.NomorBarcode', $barcode)
        ->first();
    }

    /**
     * Get collection with catalog details
     */
    public function getWithCatalog($collectionId)
    {
        return $this->select('
            collections.*,
            catalogs.Title,
            catalogs.Author,
            catalogs.Publisher,
            catalogs.ControlNumber
        ')
        ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left')
        ->where('collections.ID', $collectionId)
        ->first();
    }

    /**
     * Check if barcode exists
     */
    public function barcodeExists($barcode)
    {
        return $this->where('NomorBarcode', $barcode)->countAllResults() > 0;
    }

    /**
     * Get all collections for a catalog
     */
    public function getByCatalogId($catalogId)
    {
        return $this->where('Catalog_id', $catalogId)->findAll();
    }
}
