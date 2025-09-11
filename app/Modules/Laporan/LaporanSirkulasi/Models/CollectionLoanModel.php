<?php

namespace LaporanSirkulasi\Models;

class CollectionLoanModel extends \App\Models\BaseModel
{
      protected $DBGroup          = 'data';
      protected $table            = '';
      protected $returnType       = 'object';
      protected $allowedFields    = [];


      public function getPeminjaman($startDate = null, $endDate = null, $limit = null, $offset = 0)
      {
            $subquery = "SELECT 
                        collectionloanitems.`LoanDate` AS TglPinjam,
                        collectionloanitems.`DueDate` AS TglJatuhTempo,
                        collectionloanitems.`ActualReturn` AS TglDikembalikan,
                        DATEDIFF(collectionloanitems.`DueDate`, collectionloanitems.`ActualReturn`) AS JumlahHariTelat, 
                        collections.`NoInduk` AS no_induk,
                        CONCAT('<b>', catalogs.`Title`, '</b>','<br/>',
                        (CASE 
                              WHEN catalogs.`Worksheet_id` <> 4 
                              AND catalogs.`Edition` IS NOT NULL 
                              AND NOT LENGTH(catalogs.`Edition`) = 0 
                              THEN CONCAT('<br/>', catalogs.`Edition`) 
                              ELSE (CASE WHEN EDISISERIAL IS NOT NULL 
                                    THEN CONCAT('<br/>', EDISISERIAL) 
                                    ELSE '' END) 
                        END),
                        '<br/>', catalogs.`PublishLocation`, ' ', catalogs.`Publisher`, ' ', catalogs.`PublishYear`, 
                        '<br/>', catalogs.`Subject`, '<br/>', catalogs.`DeweyNo`) AS DataBib,
                        members.`MemberNo` AS NoAnggota,
                        members.`FullName` AS NamaAnggota,
                        jenis_kelamin.`Name` AS J_kelamin,
                        master_range_umur.`Keterangan` AS umur,
                        master_kelas_besar.`namakelas` AS nomor_klass,
                        u1.`FullName` AS PetugasPeminjaman,
                        u2.`FullName` AS PetugasPengembalian
                        FROM collectionloanitems
                        INNER JOIN collections ON collections.ID = collectionloanitems.Collection_id
                        LEFT JOIN catalogs ON catalogs.ID = collections.Catalog_id
                        INNER JOIN members ON members.ID = collectionloanitems.member_id
                        LEFT JOIN users u1 ON u1.ID = collectionloanitems.CreateBy
                        LEFT JOIN users u2 ON u2.ID = collectionloanitems.UpdateBy
                        LEFT JOIN locations ON collections.Location_Id = locations.ID 
                        LEFT JOIN jenis_kelamin ON members.Sex_id = jenis_kelamin.ID 
                        LEFT JOIN master_range_umur ON TIMESTAMPDIFF(YEAR, members.DateOfBirth, CURDATE()) 
                        BETWEEN master_range_umur.`umur1` AND master_range_umur.`umur2`
                        LEFT JOIN master_kelas_besar ON SUBSTR(catalogs.DeweyNo,1,1) = SUBSTR(master_kelas_besar.kdKelas,1,1)";
            $builder = $this->db->table("($subquery) as peminjaman");

            // filter date kalau ada
            if ($startDate && $endDate) {
                  $builder->where('TglPinjam  >=', $startDate)
                        ->where('TglPinjam  <=', $endDate);
            }

            // order & limit
            $builder->orderBy('TglPinjam ', 'DESC');
            if ($limit !== null) {
                  $builder->limit($limit, $offset);
            }

            return $builder; // penting → return builder, bukan langsung result
      }
}
