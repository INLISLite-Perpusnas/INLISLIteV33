<?php

namespace Auth\Models;

use CodeIgniter\Model;

class UserModel extends \Base\Models\BaseModel
{
	protected $DBGroup              = 'data';
    protected $table                = 'users';
    protected $primaryKey           = 'id';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;

	protected $useTimestamps 		= false;
	// protected $dateFormat           = 'datetime';
    // protected $createdField         = 'date_created';
    // protected $updatedField         = 'date_modified';
}
