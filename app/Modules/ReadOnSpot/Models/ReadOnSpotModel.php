<?php

namespace ReadOnSpot\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class ReadOnSpotModel extends Model
{
    protected $table = 'bacaditempat';
    protected $DBGroup = 'data';
    protected $primaryKey = 'ID';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields 		= false;
      protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';



    /**
     * Get today's reading data with book and member details
     */
    public function getTodayData($locationId)
    {
        $today = date('Y-m-d');
        
        return $this->select('
            bacaditempat.*,
            catalogs.Title as JudulBuku,
            collections.NomorBarcode as Barcode,
            COALESCE(members.Fullname, bacaditempat.Nama) as NamaAnggota,
            COALESCE(members.MemberNo, "-") as NoAnggota,
            IF(bacaditempat.Member_id IS NULL, 1, 0) as IsNonAnggota
        ')
        ->join('collections', 'collections.ID = bacaditempat.collection_id', 'left')
        ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left')
        ->join('members', 'members.ID = bacaditempat.Member_id', 'left')
        ->where('bacaditempat.Location_Id', $locationId)
        ->where('DATE(bacaditempat.CreateDate)', $today)
        ->orderBy('bacaditempat.CreateDate', 'DESC')
        ->findAll();
    }

    /**
     * Get reading statistics for dashboard
     */
    public function getStatistics($locationId, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->where('Location_Id', $locationId);
        
        if ($dateFrom) {
            $builder->where('DATE(CreateDate) >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('DATE(CreateDate) <=', $dateTo);
        }
        
        return [
            'total_visitors' => $builder->countAllResults(false),
            'total_books' => $builder->distinct()->countAllResults('collection_id', false),
            'active_reading' => $builder->where('Is_return', '0')->countAllResults()
        ];
    }

    /**
     * Mark book as returned
     */
    public function markAsReturned($id)
    {
        return $this->update($id, ['Is_return' => '1']);
    }
}