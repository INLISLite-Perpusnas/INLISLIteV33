<?php

namespace UsulanKoleksi\Models;

class UsulanKoleksiModel extends \Base\Models\BaseModel
{
    protected $DBGroup = 'data';
    protected $table = 'requestcatalog';
    protected $primaryKey = 'ID';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
}