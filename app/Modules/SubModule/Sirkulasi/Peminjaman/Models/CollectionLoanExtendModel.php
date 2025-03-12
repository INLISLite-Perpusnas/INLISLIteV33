<?php

namespace Peminjaman\Models;

class CollectionLoanExtendModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'collectionloanextends';
    protected $primaryKey 			= 'id';
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
