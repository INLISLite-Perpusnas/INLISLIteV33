<?php

namespace PeraturanPeminjamanTanggal\Models;

class PeraturanPeminjamanTanggalModel extends \App\Models\BaseModel
{
    protected $DBGroup              = 'default';
    protected $table      			= 'peraturan_peminjaman_tanggal';
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
