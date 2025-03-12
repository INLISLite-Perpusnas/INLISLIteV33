<?php

namespace Media\Models;

class MediaModel extends \App\Models\BaseModel
{
	protected $DBGroup              = 'backend';
    protected $table      			= 't_page';
    protected $primaryKey 			= 'id';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'created_at';
    protected $updatedField  		= 'updated_at';
    protected $deletedField  		= 'deleted_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
