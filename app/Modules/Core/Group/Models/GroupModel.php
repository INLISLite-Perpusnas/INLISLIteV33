<?php

namespace Group\Models;

use Myth\Auth\Authorization\PermissionModel;

class GroupModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'data';
    protected $table 				= 'auth_groups'; 
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

    public function getPermissions(int $groupId): array
    {
        $permissionModel = model(PermissionModel::class);
        $fromGroup = $permissionModel
            ->select('auth_permissions.*')
            ->join('auth_groups_permissions', 'auth_groups_permissions.permission_id = auth_permissions.id', 'inner')
            ->where('group_id', $groupId)
            ->findAll();

        $found = [];
        foreach ($fromGroup as $permission)
        {
            $found[$permission['id']] = $permission;
        }

        return $found;
    }
}
