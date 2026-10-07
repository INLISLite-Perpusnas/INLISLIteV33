<?php

namespace ProfilCms\Models;

use CodeIgniter\Model;

class ProfilModel extends Model
{
    protected $table            = 'profil_perpustakaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['deskripsi', 'image', 'created_at', 'created_by', 'updated_at', 'updated_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getProfil()
    {
        return $this->first();
    }
}
