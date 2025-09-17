<?php

namespace Group\Models;

class GroupPermissionModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'data';
    protected $table 				= 'auth_groups_permissions'; 
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
