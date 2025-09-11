<?php

namespace Katalog\Models;

class ArtikelModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'serial_articles';
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

        return $conn->table('serial_articles')->join('catalogs', 'catalogs.ID = serial_articles.Catalog_id')->where('serial_articles.ID', $id)->get()->getRowArray();
    }

    // get data by id
    // function get_by_id($id)
    // {
    //     $conn      = \Config\Database::connect('data');
    //     $db = $conn->query("select catalog_ruas.*,serial_articles.IsOPAC FROM serial_articles INNER JOIN catalog_ruas ON serial_articles.ID = catalog_ruas.CatalogId WHERE serial_articles.id = $id");
    //     return $db->getResult();
    // }

    // update data
    function update_action($id, $data)
    {
        $conn = \Config\Database::connect('data');
        $db   = $conn->table($this->table);

        return $db->where('id', $id)
            ->update($data);
    }

    // delete data
    function delete_action($id)
    {
        $conn      = \Config\Database::connect('data');
        $db = $conn->table($this->table);

        return $db->where($this->table, $id)
            ->delete();
    }
}
