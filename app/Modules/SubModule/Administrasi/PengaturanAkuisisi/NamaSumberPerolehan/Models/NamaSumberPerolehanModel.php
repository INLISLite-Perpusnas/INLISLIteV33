<?php

namespace NamaSumberPerolehan\Models;

class NamaSumberPerolehanModel extends \App\Models\BaseModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'partners';
    protected $primaryKey 			= 'ID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $allowedFields        = ['Name', 'Address', 'Phone', 'Fax', 'Province', 'City', 'Branch_id', 'CreateBy', 'CreateTerminal', 'UpdateBy', 'UpdateTerminal'];
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
