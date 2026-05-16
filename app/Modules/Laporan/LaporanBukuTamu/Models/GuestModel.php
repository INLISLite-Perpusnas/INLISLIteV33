<?php

namespace LaporanBukuTamu\Models;

class GuestModel extends \App\Models\BaseModel
{
    protected $DBGroup          = 'data';
    protected $table            = ''; 
    protected $returnType       = 'object';
    protected $allowedFields    = [];
  

    public function getPengunjung($startDate = null, $endDate = null, $limit = null, $offset = 0)
    {
       $subquery = "
              SELECT 'Anggota' AS ket,
                     DATE_FORMAT(mg.CreateDate, '%Y-%m-%d') AS tgl_kunjungan,
                     mg.CreateDate AS periode,
                     ll.Name AS lokasi,
                     ll.ID AS Library_id,
                     l.Name AS lok_ruang,
                     mg.NoAnggota AS no_pengunjung,
                     mg.Nama AS nama,
                     jk.Name AS gender,
                     mp.Pekerjaan AS pekerjaan,
                     md.Nama AS pendidikan,
                     tk.TujuanKunjungan AS tujuan,
                     mg.Information AS info
              FROM memberguesses mg
              LEFT JOIN members m ON m.MemberNo = mg.NoAnggota
              LEFT JOIN locations l ON l.ID = mg.Location_Id 
              LEFT JOIN location_library ll ON ll.ID = l.LocationLibrary_id
              LEFT JOIN branchs b ON b.ID = mg.Branch_id
              LEFT JOIN jenis_kelamin jk ON jk.ID = m.Sex_id 
              LEFT JOIN master_pekerjaan mp ON mp.id = m.Job_id 
              LEFT JOIN master_pendidikan md ON md.id = m.EducationLevel_id
              LEFT JOIN tujuan_kunjungan tk ON tk.ID = mg.TujuanKunjungan_Id 
              WHERE mg.NoAnggota IS NOT NULL AND m.MemberNo IS NOT NULL

              UNION ALL

              SELECT 'Non Anggota' AS ket,
                     DATE_FORMAT(mg.CreateDate, '%Y-%m-%d') AS tgl_kunjungan,
                     mg.CreateDate AS periode,
                     ll.Name AS lokasi,
                     ll.ID AS Library_id,
                     l.Name AS lok_ruang,
                     mg.NoPengunjung AS no_pengunjung,
                     mg.Nama AS nama,
                     jk.Name AS gender,
                     mp.Pekerjaan AS pekerjaan,
                     md.Nama AS pendidikan,
                     tk.TujuanKunjungan AS tujuan,
                     mg.Information AS info
              FROM memberguesses mg
              LEFT JOIN locations l ON l.ID = mg.Location_Id
              LEFT JOIN location_library ll ON ll.ID = l.LocationLibrary_id
              LEFT JOIN branchs b ON b.ID = mg.Branch_id
              LEFT JOIN jenis_kelamin jk ON jk.ID = mg.JenisKelamin_id
              LEFT JOIN master_pekerjaan mp ON mp.id = mg.Profesi_id
              LEFT JOIN master_pendidikan md ON md.id = mg.PendidikanTerakhir_id
              LEFT JOIN tujuan_kunjungan tk ON tk.ID = mg.TujuanKunjungan_Id
              WHERE mg.NoAnggota IS NULL 

              UNION ALL

              SELECT 'Rombongan' AS ket,
                     DATE_FORMAT(gg.CreateDate, '%Y-%m-%d') AS tgl_kunjungan,
                     gg.CreateDate AS periode,
                     ll.Name AS lokasi,
                     ll.ID AS Library_id,
                     l.Name AS lok_ruang,
                     gg.NoPengunjung AS no_pengunjung,
                     gg.NamaKetua AS nama,
                     CONCAT(IFNULL(CONCAT(gg.CountLaki, ' Laki-laki'),''),'<br>', IFNULL(CONCAT(gg.CountPerempuan, ' Perempuan'),'')) AS gender,
                     CONCAT(IFNULL(CONCAT(gg.CountPNS, ' PNS','<br>'),''), IFNULL(CONCAT(gg.CountPSwasta, ' Pegawai Swasta','<br>'),''), 
                            IFNULL(CONCAT(gg.CountPeneliti, ' Peneliti','<br>'),''), IFNULL(CONCAT(gg.CountGuru, ' Guru','<br>'),''), 
                            IFNULL(CONCAT(gg.CountDosen, ' Dosen','<br>'),''), IFNULL(CONCAT(gg.CountPensiunan, ' Pensiunan','<br>'),''), 
                            IFNULL(CONCAT(gg.CountTNI, ' TNI','<br>'),''), IFNULL(CONCAT(gg.CountWiraswasta, ' Wiraswasta','<br>'),''), 
                            IFNULL(CONCAT(gg.CountPelajar, ' Pelajar','<br>'),''), IFNULL(CONCAT(gg.CountMahasiswa, ' Mahasiswa','<br>'),''), 
                            IFNULL(CONCAT(gg.CountLainnya, ' Lainnya'),'')) AS pekerjaan,
                     CONCAT(IFNULL(CONCAT(gg.CountSD, ' SD','<br>'),''), IFNULL(CONCAT(gg.CountSMP, ' SMP','<br>'),''), 
                            IFNULL(CONCAT(gg.CountSMA, ' SMA','<br>'),''), IFNULL(CONCAT(gg.CountD1, ' D1','<br>'),''), 
                            IFNULL(CONCAT(gg.CountD2, ' D2','<br>'),''), IFNULL(CONCAT(gg.CountD3, ' D3','<br>'),''), 
                            IFNULL(CONCAT(gg.CountS1, ' S1','<br>'),''), IFNULL(CONCAT(gg.CountS2, ' S2','<br>'),''), 
                            IFNULL(CONCAT(gg.CountS3, ' S3'),'')) AS pendidikan,
                     tk.TujuanKunjungan AS tujuan,
                     gg.Information AS info
              FROM groupguesses gg
              LEFT JOIN locations l ON l.ID = gg.Location_Id
              LEFT JOIN location_library ll ON ll.ID = l.LocationLibrary_id
              LEFT JOIN branchs b ON b.ID = gg.Branch_id
              LEFT JOIN tujuan_kunjungan tk ON tk.ID = gg.TujuanKunjungan_Id
       ";
       $builder = $this->db->table("($subquery) as pengunjung");

       // filter date kalau ada
       if ($startDate && $endDate) {
              $builder->where('periode >=', $startDate)
                     ->where('periode <=', $endDate);
       }

       // order & limit
       $builder->orderBy('tgl_kunjungan', 'DESC');
       if ($limit !== null) {
              $builder->limit($limit, $offset);
       }

       return $builder; // penting → return builder, bukan langsung result
    }

}



