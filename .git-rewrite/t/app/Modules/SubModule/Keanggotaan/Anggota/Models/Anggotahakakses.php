<?php

namespace Anggota\Models;


class Anggotahakakses extends \App\Models\BaseModel
{
    protected $DBGroup              = 'default';
    protected $table      			= 'memberloanauthorizelocation';
    protected $primaryKey 			= 'DataID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    // protected $allowedFields = [
    //     'id', 'slug', 'name','MemberNo','IdentityNo','PlaceOfBirth','DateOfBirth','Address','AddressNow','Phone','InstitutionName','InstitutionAddress','InstitutionPhone','MotherName','Email','LoanReturnLateCount','', 'NoHp','Provincy','City','ProvincyNow','CityNow','Kecamatan','KecamatanNow','Kelurahan','KelurahanNow','RT','RTNow','RW','RWNow','Tahunajaran','KeteranganLain','IsLunasBiayaPendaftaran','BiayaPendaftaran','TanggalBebasPustaka', 'RegisterDate','EndDate' ,'description', 'sort',  'active', 'created_by', 'updated_by'
    // ];
 
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    // // protected $deletedField  = 'deleted_at';
    // protected $createdField  		= 'created_at';
    // protected $updatedField  		= 'updated_at';
    // protected $deletedField  		= 'deleted_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;

    
}
