<?php

namespace Permission\Models;

class PermissionModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'default';
    protected $table 				= 'auth_permissions'; 
    protected $primaryKey 			= 'id';
    protected $returnType 			= 'object';
    protected $useSoftDeletes 		= false;
	protected $protectFields 		= false;
    protected $useTimestamps 		= false;
    protected $createdField 		= 'created_at';
    protected $updatedField 		= 'updated_at';
    protected $deletedField 		= 'deleted_at';
    protected $validationRules 		= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
