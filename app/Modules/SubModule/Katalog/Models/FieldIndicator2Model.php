<?php

namespace Katalog\Models;

class FieldIndicator2Model extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'fieldindicator2s';
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
