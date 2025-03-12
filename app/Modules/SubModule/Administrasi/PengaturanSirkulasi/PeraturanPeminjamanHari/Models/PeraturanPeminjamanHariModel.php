<?php

namespace PeraturanPeminjamanHari\Models;

class PeraturanPeminjamanHariModel extends \App\Models\BaseModel
{
    protected $DBGroup              = 'data';
    protected $table      			= 'peraturan_peminjaman_hari';
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
