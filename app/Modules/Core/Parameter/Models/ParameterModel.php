<?php

namespace Parameter\Models;

class ParameterModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'data';
    protected $table 				= 'settingparameters';
    protected $primaryKey 			= 'ID';
    protected $returnType 			= 'object';
    protected $useSoftDeletes 		= false;
	protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField 		= 'CreateDate';
    protected $updatedField 		= 'UpdateDate';
    protected $validationRules 		= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
