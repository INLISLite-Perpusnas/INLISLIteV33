<?php

namespace Referensi\Models;

class RefItemModel extends \Base\Models\DataModel
{
    protected $table      			= 'refferenceitems';
    protected $primaryKey 			= 'RefferenceItemsID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    protected $deletedField  		= 'deleted_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
