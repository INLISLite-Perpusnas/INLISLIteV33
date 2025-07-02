<?php
if (!function_exists('get_member')) {
    function get_member($member_no = null)
    {        
		$model = new \Anggota\Models\AnggotaModel();

		$data = $model->where('MemberNo',$member_no)->get()->getRow();
        return $data;
    }
}

if (!function_exists('get_MemberNo')) {
    function get_MemberNo()
    {
        $baseModel = new \hamkamannan\adminigniter\Models\BaseModel();
        $baseModel->setTable('t_anggota');
        $kode = $baseModel
			->select ('RIGHT(MemberNo,4) as MemberNo', false)
			->orderBy('MemberNo','DESC')
			->limit(1)->get()->getRowArray();

        if (empty($kode['MemberNo'])){
            $no=1;
        }else{
            $no=intval($kode['MemberNo']) + 1; }
        $tgl= date('Ymd');
        $batas = str_pad($no, 4, "0", STR_PAD_LEFT);
        $MemberNo = $tgl.$batas;
        return $MemberNo;
    }
    
}

if (!function_exists('get_member_no')) {
    function get_member_no()
    {        
		$no = 1;
		$anggotaModel = new \Anggota\Models\AnggotaModel();
		$query = $anggotaModel
			->where('LENGTH(MemberNo) >=', 12, FALSE)
			->orderBy('id','desc')
			->limit(1);

		$data = $query->get()->getRow();

		if(!empty($data)){
			$no = intval(substr($data->MemberNo, -4, 4)) + 1;
		} 

		$today = date('Ymd');
        $increment = str_pad($no, 4, "0", STR_PAD_LEFT);
        $member_no = $today.$increment; 

        return $member_no;
    }
}

if (!function_exists('get_sumbangan')) {
    function get_sumbangan($member_id = null)
    {        
		$model = new \Sumbangan\Models\SumbanganModel();
        $query = $model
            ->select('sumbangan.*');

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
        return $data;
    }
}

if (!function_exists('get_perpanjangan')) {
    function get_perpanjangan($member_id = null)
    {        
		$model = new \PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
        $query = $model
            ->select('member_perpanjangan.*');

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
    //   dd($data);
        return $data;
    }
}

if (!function_exists('get_peminjaman')) {
    function get_peminjaman($member_id = null,$DBGroup='data')
    {        
		$model = new \Peminjaman\Models\CollectionLoanItemModel();
        $query = $model
            ->select('collectionloanitems.*');

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
        return $data;
    }
}

if (!function_exists('get_pelanggaran')) {
    function get_pelanggaran($member_id = null,$DBGroup='data')
    {        
		$baseModel = new \Anggota\Models\pelanggaranModel();
		$query = $baseModel;
        // dd($query);

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
        return $data;
    }
}

if (!function_exists('tgl_indonesia')) {
function tgl_indonesia($tgl){ 
    $tanggal = substr($tgl,8,2);
    $nama_bulan = array("Januari", "Februari", "Maret", "April", "Mei", 
            "Juni", "Juli", "Agustus", "September", 
            "Oktober", "November", "Desember");
    $bulan = $nama_bulan[substr($tgl,5,2) - 1];
    $tahun = substr($tgl,0,4);
    return $tanggal.' '.$bulan.' '.$tahun;       
}
}

if ( ! function_exists('date_indo'))
{
    function date_indo($tgl)
    {
        $ubah = gmdate($tgl, time()+60*60*8);
        $pecah = explode("-",$ubah);
        $tanggal = $pecah[2];
        $bulan = bulan($pecah[1]);
        $tahun = $pecah[0];
        return $tanggal.' '.$bulan.' '.$tahun;
    }
}
  
if ( ! function_exists('bulan'))
{
    function bulan($bln)
    {
        switch ($bln)
        {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }
}



?>

