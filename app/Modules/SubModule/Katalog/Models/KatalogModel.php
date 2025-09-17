<?php

namespace Katalog\Models;

class KatalogModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'catalogs';
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
    function get_null_quarantine()
    {
        // return $this->findAll();
        return $this->where('IsQUARANTINE', null)
            ->findAll();
    }

    // get all 
    function get_all_quarantine()
    {
        return $this->where('IsQUARANTINE', 1)
            ->findAll();
    }

    // get data by id
    function get_all_by_id($id)
    {
        $conn      = \Config\Database::connect('data');
        // $db = $conn->table($this->table);
        // return $db->where('ID', $id)
        //     ->get()
        //     ->getRowArray();

        return $conn->table('catalogs')->join('worksheets', 'worksheets.ID = catalogs.Worksheet_id')->where('catalogs.ID', $id)->get()->getRowArray();
    }

    // get data by id
    function get_by_id($id)
    {
        $conn      = \Config\Database::connect('data');
        $db = $conn->query("select catalog_ruas.*,catalogs.IsOPAC FROM catalogs INNER JOIN catalog_ruas ON catalogs.ID = catalog_ruas.CatalogId WHERE catalogs.id = $id");
        return $db->getResult();
    }

}
