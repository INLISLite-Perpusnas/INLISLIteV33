<?php

namespace User\Models;

use Myth\Auth\Models\UserModel as MythModel;
use Myth\Auth\Authorization\GroupModel;

class UserModel extends MythModel
{
	protected $DBGroup 			= 'default';
    protected $protectFields 	= false;
	protected $useSoftDeletes 	= false;

    public function getGroups($user_id)
    {
        $roles = [];
		$groups = (new GroupModel())->getGroupsForUser($user_id);

		foreach ($groups as $group)
		{
			$roles[$group['group_id']] = strtolower($group['name']);
		}

        return $roles;
	}
}
