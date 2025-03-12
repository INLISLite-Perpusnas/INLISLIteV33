<?php

namespace JenisPerpustakaan\Models;

class JenisPerpustakaanModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'jenis_perpustakaan';
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
