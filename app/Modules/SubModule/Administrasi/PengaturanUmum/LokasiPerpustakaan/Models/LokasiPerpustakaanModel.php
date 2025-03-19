<?php

namespace LokasiPerpustakaan\Models;

class LokasiPerpustakaanModel extends \App\Models\BaseModel
{
    protected $DBGroup              = 'data';
	protected $table      			= 'location_library';
    protected $primaryKey 			= 'ID';
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
