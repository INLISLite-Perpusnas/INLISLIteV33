<?php

namespace Katalog\Models;

class FieldIndicator1Model extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'fieldindicator1s';
    protected $primaryKey 			= 'Field_id';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
