<?php

namespace KelompokPelanggaran\Models;

class KelompokPelanggaranModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'kelompok_pelanggaran';
    protected $primaryKey 			= 'id';
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
