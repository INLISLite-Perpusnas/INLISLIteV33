<?php

namespace JenisAnggota\Controllers;

use \CodeIgniter\Files\File;

class JenisAnggota extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $jenisanggotaModel;
    public $defaultlokasi;
    public $defaultbahan;
    public $jenisbahan;
    public $lokasi;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->jenisanggotaModel = new \JenisAnggota\Models\JenisAnggotaModel();
        $this->defaultlokasi = new \JenisAnggota\Models\DefaultLokasiModel();
        $this->lokasi = new \JenisAnggota\Models\LokasiModel();
        $this->jenisbahan = new \JenisBahan\Models\JenisBahanModel();
        $this->defaultbahan = new \JenisAnggota\Models\DefaultBahanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/jenisanggota/';
        helper('reference');
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }
    public function index()
    {
        $this->data['title'] = 'Jenis Anggota';
        echo view('JenisAnggota\Views\list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('master-jenis-anggota');
        }
        $jenisanggotaDelete = $this->jenisanggotaModel->delete($id);
        if ($jenisanggotaDelete) {
            set_message('toastr_msg', 'Jenis Anggota berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('master-jenis-anggota');
        } else {
            set_message('toastr_msg', 'Jenis Anggota gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', 'Jenis Anggota gagal dihapus');
            return redirect()->to('master-jenis-anggota');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $jenisanggotaUpdate = $this->jenisanggotaModel->update($id, array($field => $value));

        if ($jenisanggotaUpdate) {
            set_message('toastr_msg', 'Jenis Anggota berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Jenis Anggota gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/jenis-anggota');
    }

    public function DefaultLokasi($id)
    {
        $this->data['title'] = 'Default Lokasi';
        $db = db_connect('data');

        $locations = $this->lokasi->where('Branch_id', user()->branch_id)->findAll();

        // Perform the join query
        $query = $db->table('location_library_default')
            ->join('location_library', 'location_library.ID = location_library_default.Location_Library_id')
            ->where('location_library_default.JenisAnggota_id', $id)
            ->select('location_library_default.*, location_library.Name')
            ->get();

        // Fetch the joined data
        $joinedData = $query->getResult();
       

        $db = db_connect('data');
        $builder = $db->table('collectioncategorysdefault');
        $builder->select('CollectionCategory_id,JenisAnggota_id');
        $builder->where('JenisAnggota_id', $id);


        $query = $builder->get();

        $jenisbahans = $query->getResult();
       
        $jenisbahanIds = [];
        foreach ($jenisbahans as $jenisbahan) {
            $jenisbahanIds[] = $jenisbahan->CollectionCategory_id;
        }

      
        $JenisAnggota_id = $id;
        $this->data['joinedData'] = $joinedData;
        $this->data['locations'] = $locations;
        // $this->data['locationslibrary'] = $locationslibrary;
        $this->data['JenisAnggota_id'] = $JenisAnggota_id;


        return view('JenisAnggota\Views\defaultlokasi', $this->data);
    }

    public function save()
    {
        $locationID = $this->request->getPost('Location_Library_id');
        // dd($locationID);
        $jenisAnggotaID = $this->request->getPost('JenisAnggota_id');

        // Insert the checked item into the location_library_default table
        // You'll need to modify this to match your model and table structure
        $InsertDefaultlokasi = $this->defaultlokasi->insert(['JenisAnggota_id' => $jenisAnggotaID, 'Location_Library_id' => $locationID]);
        return json_encode(['success' => true]);
    }

    public function deletedefaultlokasi($id)
    {
        $this->defaultlokasi->delete($id);
        return $this->response->setStatusCode(200)->setBody('Record deleted successfully');
    }

    public function DefaultBahan($id)
    {
        $this->data['title'] = 'Default Jenis Bahan';
        $db = db_connect('data');
        // Perform the join query
        $query = $db->table('collectioncategorysdefault')
            ->join('collectioncategorys', 'collectioncategorys.ID = collectioncategorysdefault.CollectionCategory_id')
            ->where('collectioncategorysdefault.JenisAnggota_id', $id)
            ->select('collectioncategorysdefault.*, collectioncategorys.Name')
            ->get();

        // Fetch the joined data
        $joinedData = $query->getResult();

       $CollectionCategorys = $db->table('collectioncategorys')->get()->getResult();

        $JenisAnggota_id = $id;

        $this->data['joinedData'] = $joinedData;
        $this->data['CollectionCategorys'] = $CollectionCategorys;
        $this->data['JenisAnggota_id'] = $JenisAnggota_id;
 


        return view('JenisAnggota\Views\defaultbahan', $this->data);
    }

    public function savejenisbahan()
    {
        $jenisAnggotaID = $this->request->getPost('JenisAnggota_id');
        $CollectionCategory = $this->request->getPost('CollectionCategory_id');
        // dd($locationID);


        // Insert the checked item into the location_library_default table
        // You'll need to modify this to match your model and table structure
        $InsertDefaultlokasi = $this->defaultbahan->insert(['JenisAnggota_id' => $jenisAnggotaID, 'CollectionCategory_id' => $CollectionCategory]);
        return $this->response->setStatusCode(200)->setBody('Record deleted successfully');
    }

    public function deletedefaultbahan($id)
    {

        $this->defaultbahan->delete($id);
        return $this->response->setStatusCode(200)->setBody('Record deleted successfully');
    }
}
