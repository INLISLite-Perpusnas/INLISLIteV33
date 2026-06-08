<?php

namespace RuasDataBibliografis\Models;
use CodeIgniter\Model;
class RuasDataBibliografisModel extends Model
{
	protected $DBGroup              = 'data';
    protected $table      			= 'worksheetfields';
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
}
