<?php

namespace Peminjaman\Models;

class LoanDigitalModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'loans_digital';
    protected $primaryKey 			= 'ID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
     protected $createdField  		= 'created_at';
    protected $updatedField  		= 'updated_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
