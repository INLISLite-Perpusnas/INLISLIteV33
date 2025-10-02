<?php

namespace LaporanBacaDitempat\Models;

class GuestModel extends \App\Models\BaseModel
{
    protected $DBGroup             = 'data';
    protected $table      		= 'bacaditempat';
    protected $primaryKey 		= 'ID';
    protected $returnType     	= 'object';
    protected $useSoftDeletes 	= false;
    protected $protectFields 	= false;
    protected $useTimestamps 	= true;
    protected $createdField  	= 'CreateDate';
    protected $updatedField  	= 'UpdateDate';
    protected $deletedField  	= 'DeleteDate';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
  

    public function getPengunjung($startDate = null, $endDate = null, $limit = null, $offset = 0)
    {
       //  $subquery = "
       //      SELECT 
       //          bacaditempat.NoPengunjung AS no_pengunjung,
       //          location_library.Name AS lokasi,
       //          locations.Name AS lok_ruang,
       //          NoInduk AS noinduk,
       //          members.MemberNo AS noanggota,
       //          (CASE WHEN bacaditempat.Member_id IS NULL 
       //                THEN bacaditempat.NoPengunjung 
       //                ELSE members.FullName END) AS nama,
       //          bacaditempat.CreateDate AS tgl_kunjungan,
       //          bacaditempat.CreateDate AS periode,

       //          -- field tambahan filter
       //          jenis_kelamin.Name AS gender,
       //          pekerjaan.Name AS pekerjaan,
       //          pendidikan.Name AS pendidikan,
       //          bacaditempat.Keterangan AS ket,
       //          bacaditempat.TujuanKunjungan AS tujuan,
       //          bacaditempat.InformasiDicari AS info

       //      FROM bacaditempat
       //      INNER JOIN collections ON bacaditempat.Collection_id = collections.ID 
       //      INNER JOIN catalogs ON collections.Catalog_id = catalogs.ID 
       //      INNER JOIN worksheets ON catalogs.Worksheet_id = worksheets.ID 
       //      LEFT JOIN members ON bacaditempat.Member_id = members.ID 
       //      LEFT JOIN location_library ON collections.Location_Library_id = location_library.ID 
       //      LEFT JOIN locations ON collections.Location_id = locations.ID

       //      -- join tambahan
       //      LEFT JOIN jenis_kelamin ON members.Sex_id = jenis_kelamin.ID
       //      LEFT JOIN pekerjaan ON members.Job_id = pekerjaan.ID
       //      LEFT JOIN pendidikan ON members.EducationLevel_id = pendidikan.ID
       //  ";

        $subquery .= "  SELECT bacaditempat.NoPengunjung AS no_pengunjung,
                location_library.Name AS lokasi,
                locations.Name AS lok_ruang,
                NoInduk AS noinduk,
                members.MemberNo AS noanggota,
                (CASE WHEN bacaditempat.Member_id IS NULL 
                      THEN bacaditempat.NoPengunjung 
                      ELSE members.FullName END) AS nama,
                bacaditempat.CreateDate AS tgl_kunjungan,
                bacaditempat.CreateDate AS periode
                -- field tambahan filter
            FROM bacaditempat INNER JOIN collections ON bacaditempat.Collection_id = collections.ID 
            INNER JOIN catalogs ON collections.Catalog_id = catalogs.ID 
            INNER JOIN worksheets ON catalogs.Worksheet_id = worksheets.ID 
            LEFT JOIN members ON bacaditempat.Member_id = members.ID 
            LEFT JOIN location_library ON collections.Location_Library_id = location_library.ID 
            LEFT JOIN locations ON collections.Location_id = locations.ID ";

        $builder = $this->db->table("($subquery) as pengunjung");

        // filter date kalau ada
        if ($startDate && $endDate) {
            $builder->where('periode >=', $startDate)
                    ->where('periode <=', $endDate);
        }

        // order & limit
        $builder->orderBy('periode', 'DESC');
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder;
    }

}



