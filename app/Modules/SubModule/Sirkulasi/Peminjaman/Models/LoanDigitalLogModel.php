<?php

namespace Peminjaman\Models;

class LoanDigitalLogModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'loan_digital_logs';
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
