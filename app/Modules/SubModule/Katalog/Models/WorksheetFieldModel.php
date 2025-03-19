<?php

namespace Katalog\Models;

class WorksheetFieldModel extends \Base\Models\DataModel
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
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
