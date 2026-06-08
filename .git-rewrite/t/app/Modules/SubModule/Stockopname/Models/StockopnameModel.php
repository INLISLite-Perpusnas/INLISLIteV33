<?php

namespace Stockopname\Models;

class StockopnameModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data'; 
    protected $table      			= 'stockopname';
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


        public function getStockopnameWithSearch($search = null, $tahun = null, $status = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        
        // Apply search filters
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('ProjectName', $search)
                    ->orLike('Koordinator', $search)
                    ->orLike('Keterangan', $search)
                    ->groupEnd();
        }
        
        if (!empty($tahun)) {
            $builder->where('Tahun', $tahun);
        }
        
        
        // Apply pagination
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->orderBy('CreateDate', 'DESC')->get()->getResultArray();
    }


    public function getStockopnameCount($search = null, $tahun = null, $status = null)
    {
        $builder = $this->builder();
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('ProjectName', $search)
                    ->orLike('Koordinator', $search)
                    ->orLike('Keterangan', $search)
                    ->groupEnd();
        }
        
        if (!empty($tahun)) {
            $builder->where('tahun', $tahun);
        }
        
        if (!empty($status)) {
            $builder->where('status', $status);
        }
        
        return $builder->countAllResults();
    }
}
