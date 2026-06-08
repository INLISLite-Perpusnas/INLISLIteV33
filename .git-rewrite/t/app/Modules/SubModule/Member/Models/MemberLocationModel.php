<?php

namespace Member\Models;

class MemberLocationModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'memberloanauthorizelocation';
    protected $primaryKey 			= 'DataID';
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
