<?php

namespace Stockopname\Models;

class StockopnamedetailModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data'; 
    protected $table      			= 'stockopnamedetail';
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
  
    public function getStockopnameDetails($stockopnameId, $limit = null, $offset = null)
    {
        $builder = $this->db->table('stockopnamedetail sd')
            ->select('
                sd.*,
                c.NomorBarcode,
                c.CallNumber,
                cat.Title,
                cat.Author,
                cat.Publisher,
                prevLoc.Name as PrevLocationName,
                currLoc.Name as CurrentLocationName,
                prevStatus.Name as PrevStatusName,
                currStatus.Name as CurrentStatusName,
                prevRule.Name as PrevRuleName,
                currRule.Name as CurrentRuleName
            ')
            ->join('collections c', 'sd.CollectionID = c.id', 'left')
            ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
            ->join('locations prevLoc', 'sd.PrevLocationID = prevLoc.ID', 'left')
            ->join('locations currLoc', 'sd.CurrentLocationID = currLoc.ID', 'left')
            ->join('collectionstatus prevStatus', 'sd.PrevStatusID = prevStatus.ID', 'left')
            ->join('collectionstatus currStatus', 'sd.CurrentStatusID = currStatus.ID', 'left')
            ->join('collectionrules prevRule', 'sd.PrevCollectionRuleID = prevRule.ID', 'left')
            ->join('collectionrules currRule', 'sd.CurrentCollectionRuleID = currRule.ID', 'left')
            ->where('sd.StockOpnameID', $stockopnameId)
            ->orderBy('sd.CreateDate', 'DESC');
           

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get detail by ID with joins
     */
    public function getDetailById($id)
    {
        return $this->db->table('stockopnamedetail sd')
            ->select('
                sd.*,
                c.NomorBarcode,
                c.CallNumber,
                cat.Title,
                cat.Author,
                cat.Publisher,
                prevLoc.Name as PrevLocationName,
                currLoc.Name as CurrentLocationName,
                prevStatus.Name as PrevStatusName,
                currStatus.Name as CurrentStatusName,
                prevRule.Name as PrevRuleName,
                currRule.Name as CurrentRuleName
            ')
            ->join('collections c', 'sd.CollectionID = c.id', 'left')
            ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
            ->join('locations prevLoc', 'sd.PrevLocationID = prevLoc.ID', 'left')
            ->join('locations currLoc', 'sd.CurrentLocationID = currLoc.ID', 'left')
            ->join('collectionstatus prevStatus', 'sd.PrevStatusID = prevStatus.ID', 'left')
            ->join('collectionstatus currStatus', 'sd.CurrentStatusID = currStatus.ID', 'left')
            ->join('collectionrules prevRule', 'sd.PrevCollectionRuleID = prevRule.ID', 'left')
            ->join('collectionrules currRule', 'sd.CurrentCollectionRuleID = currRule.ID', 'left')
            ->where('sd.ID', $id)
            ->get()
            ->getRowArray();
    }



    /**
     * Get collections that are not yet in stockopname
     */
    public function getCollectionsNotInStockopname($stockopnameId, $limit = 150, $offset = 0, $search = null)
    {
        $builder = $this->db->table('collections c')
            ->select('
                c.id,
                c.NomorBarcode,
                c.CallNumber,
                cat.Title,
                cat.Author,
                cat.Publisher,
                loc.Name,
                cs.Name,
                cr.Name
            ')
            ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
            ->join('locations loc', 'c.location_id = loc.ID', 'left')
            ->join('collectionstatus cs', 'c.status_id = cs.ID', 'left')
            ->join('collectionrules cr', 'c.Rule_id = cr.ID', 'left')
            ->where('c.id NOT IN (
                SELECT CollectionID 
                FROM stockopnamedetail 
                WHERE StockOpnameID = ' . intval($stockopnameId) . '
            )', null, false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.NomorBarcode', $search)
                ->orLike('cat.Title', $search)
                ->orLike('cat.Author', $search)
                ->orLike('c.CallNumber', $search)
                ->groupEnd();
        }

        return $builder->orderBy('cat.Title', 'ASC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Get count of collections not in stockopname
     */
    public function getCollectionsNotInStockopnameCount($stockopnameId, $search = null)
    {
        $builder = $this->db->table('collections c')
            ->join('catalogs cat', 'c.catalog_id = cat.id', 'left')
            ->where('c.id NOT IN (
                SELECT CollectionID 
                FROM stockopnamedetail 
                WHERE StockOpnameID = ' . intval($stockopnameId) . '
            )', null, false);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.NomorBarcode', $search)
                ->orLike('cat.Title', $search)
                ->orLike('cat.Author', $search)
                ->orLike('c.CallNumber', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get next available ID
     */
    public function getNextId()
    {
        $result = $this->db->table($this->table)
            ->selectMax('ID')
            ->get()
            ->getRowArray();
        
        return ($result['ID'] ?? 0) + 1;
    }

    /**
     * Get stockopname detail count by stockopname ID
     */
    public function getDetailCount($stockopnameId)
    {
        return $this->where('StockOpnameID', $stockopnameId)->countAllResults();
    }

    /**
     * Get stockopname summary statistics
     */
    public function getStockopnameSummary($stockopnameId)
    {
        $total = $this->getDetailCount($stockopnameId);
        
        // Count changes by type
        $locationChanges = $this->db->table($this->table)
            ->where('StockOpnameID', $stockopnameId)
            ->where('PrevLocationID != CurrentLocationID')
            ->countAllResults();

        $statusChanges = $this->db->table($this->table)
            ->where('StockOpnameID', $stockopnameId)
            ->where('PrevStatusID != CurrentStatusID')
            ->countAllResults();

        $ruleChanges = $this->db->table($this->table)
            ->where('StockOpnameID', $stockopnameId)
            ->where('PrevCollectionRuleID != CurrentCollectionRuleID')
            ->countAllResults();

        return [
            'total_items' => $total,
            'location_changes' => $locationChanges,
            'status_changes' => $statusChanges,
            'rule_changes' => $ruleChanges
        ];
    }

    /**
     * Bulk insert stockopname details
     */
    public function bulkInsert($data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            return $this->insertBatch($data);
        } catch (\Exception $e) {
            log_message('error', 'Bulk insert stockopname details failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if collection already exists in stockopname
     */
    public function isCollectionInStockopname($stockopnameId, $collectionId)
    {
        return $this->where([
            'StockOpnameID' => $stockopnameId,
            'CollectionID' => $collectionId
        ])->first() !== null;
    }

    /**
     * Get collections with changes only
     */
    public function getCollectionsWithChanges($stockopnameId)
    {
        return $this->db->table('stockopnamedetail sd')
            ->select('
                sd.*,
                c.NomorBarcode,
                c.CallNumber,
                cat.Title,
                prevLoc.LocationName as PrevLocationName,
                currLoc.LocationName as CurrentLocationName,
                prevStatus.StatusName as PrevStatusName,
                currStatus.StatusName as CurrentStatusName,
                prevRule.RuleName as PrevRuleName,
                currRule.RuleName as CurrentRuleName
            ')
            ->join('collections c', 'sd.CollectionID = c.id', 'left')
            ->join('catalog cat', 'c.catalog_id = cat.id', 'left')
            ->join('location prevLoc', 'sd.PrevLocationID = prevLoc.ID', 'left')
            ->join('location currLoc', 'sd.CurrentLocationID = currLoc.ID', 'left')
            ->join('collectionstatus prevStatus', 'sd.PrevStatusID = prevStatus.ID', 'left')
            ->join('collectionstatus currStatus', 'sd.CurrentStatusID = currStatus.ID', 'left')
            ->join('collectionrules prevRule', 'sd.PrevCollectionRuleID = prevRule.ID', 'left')
            ->join('collectionrules currRule', 'sd.CurrentCollectionRuleID = currRule.ID', 'left')
            ->where('sd.StockOpnameID', $stockopnameId)
            ->groupStart()
                ->where('sd.PrevLocationID != sd.CurrentLocationID')
                ->orWhere('sd.PrevStatusID != sd.CurrentStatusID')
                ->orWhere('sd.PrevCollectionRuleID != sd.CurrentCollectionRuleID')
            ->groupEnd()
            ->orderBy('sd.CreateDate', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Delete all details by stockopname ID
     */
    public function deleteByStockopnameId($stockopnameId)
    {
        return $this->where('StockOpnameID', $stockopnameId)->delete();
    }

public function countStockopnameDetailsByLocationAndStatus($stockopnameId)
{
    // 1. Ambil semua kemungkinan nama status untuk dijadikan header kolom
    $allStatuses = $this->db->table('collectionstatus')->select('Name')->get()->getResultArray();
    $statusNames = array_column($allStatuses, 'Name');
    $statusTemplate = array_fill_keys($statusNames, 0);

    // 2. Ambil data jumlah per lokasi dan status (seperti query sebelumnya)
    $builder = $this->db->table('stockopnamedetail sd')
        ->select('
            currLoc.Name as CurrentLocationName,
            currStatus.Name as CurrentStatusName,
            COUNT(sd.ID) as total
        ')
        ->join('locations currLoc', 'sd.CurrentLocationID = currLoc.ID', 'left')
        ->join('collectionstatus currStatus', 'sd.CurrentStatusID = currStatus.ID', 'left')
        ->where('sd.StockOpnameID', $stockopnameId)
        ->groupBy(['sd.CurrentLocationID', 'sd.CurrentStatusID', 'currLoc.Name', 'currStatus.Name']);
    
    $counts = $builder->get()->getResultArray();

    // 3. Proses data mentah menjadi format pivot table
    $summary = [];
    foreach ($counts as $row) {
        $location = $row['CurrentLocationName'];
        $status = $row['CurrentStatusName'];
        $total = (int)$row['total'];

        // Jika lokasi ini belum ada di array summary, inisialisasi
        if (!isset($summary[$location])) {
            $summary[$location] = array_merge(
                ['Lokasi' => $location, 'Jumlah Koleksi' => 0],
                $statusTemplate
            );
        }

        // Isi jumlah untuk status yang sesuai
        if (isset($summary[$location][$status])) {
            $summary[$location][$status] = $total;
        }
        
        // Akumulasi total koleksi per lokasi
        $summary[$location]['Jumlah Koleksi'] += $total;
    }

    // 4. Kembalikan hasil sebagai array non-asosiatif
    return array_values($summary);
}

}
