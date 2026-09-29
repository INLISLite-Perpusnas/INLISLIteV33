<?php

namespace ProfilCms\Controllers;

use ProfilCms\Models\ProfilModel;
use ProfilCms\Models\LayananModel;

class Profil extends \Base\Controllers\BaseController
{
    protected $profilModel;
    protected $layananModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->profilModel  = new ProfilModel();
        $this->layananModel = new LayananModel();
        $this->lokasiModel  = new \LokasiRuang\Models\LokasiRuangModel();
    }

    public function index()
    {
        $this->data['title']     = 'Kelola Profil & Layanan Perpustakaan';
        $this->data['profil']    = $this->profilModel->getProfil();
        $this->data['lokasiList']= $this->lokasiModel->findAll();

        return view('ProfilCms\Views\list', $this->data);
    }

    public function updateProfil()
    {
        $profil    = $this->profilModel->getProfil();
        $deskripsi = $this->request->getPost('deskripsi');

        $data = [
            'deskripsi'  => $deskripsi,
            'updated_by' => session()->get('user_id') ?? 1
        ];

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $uploadPath = FCPATH . 'uploads/profil';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Hapus gambar profil utama lama dari disk jika ada
            if ($profil && !empty($profil['image']) && file_exists(FCPATH . 'uploads/profil/' . $profil['image'])) {
                @unlink(FCPATH . 'uploads/profil/' . $profil['image']);
            }

            $image->move($uploadPath, $newName);
            $data['image'] = $newName;
        }

        if ($profil) {
            $this->profilModel->update($profil['id'], $data);
        } else {
            $data['created_by'] = session()->get('user_id') ?? 1;
            $this->profilModel->insert($data);
        }

        session()->setFlashdata('swal_icon', 'success');
        session()->setFlashdata('swal_title', 'Berhasil');
        session()->setFlashdata('swal_text', 'Profil perpustakaan berhasil diperbarui.');

        return redirect()->to(base_url('cms/profil'));
    }

    public function layananDatatable()
    {
        $request     = service('request');
        $searchValue = $request->getGet('search')['value'] ?? '';
        $start       = (int)($request->getGet('start') ?? 0);
        $length      = (int)($request->getGet('length') ?? 10);

        $db      = \Config\Database::connect();
        $builder = $db->table('layanan_perpustakaan l')
                      ->select('l.*, loc.Name as nama_lokasi, loc.Code as kode_lokasi')
                      ->join('locations loc', 'loc.ID = l.location_id', 'left');

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('l.nama_layanan', $searchValue)
                    ->orLike('l.deskripsi', $searchValue)
                    ->orLike('loc.Name', $searchValue)
                    ->orLike('loc.Code', $searchValue)
                    ->orLike('l.jam_layanan', $searchValue)
                    ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $builder->orderBy('l.id', 'DESC');
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        $rows = [];
        $no = $start + 1;
        foreach ($data as $item) {
            $fotoUrl  = !empty($item['foto']) ? base_url('uploads/layanan/' . $item['foto']) : null;
            $fotoHtml = $fotoUrl ? '<img src="' . $fotoUrl . '" class="img-thumbnail" style="max-height: 60px;">' : '<span class="badge badge-secondary">Tidak ada foto</span>';

            $lokasiText = !empty($item['nama_lokasi']) ? '[' . esc($item['kode_lokasi']) . '] ' . esc($item['nama_lokasi']) : '-';

            $action = '
                <button type="button" class="btn btn-sm btn-info edit-layanan" data-id="' . $item['id'] . '"><i class="fa fa-edit"></i> Edit</button>
                <a href="' . base_url('cms/profil/layanan/delete/' . $item['id']) . '" class="btn btn-sm btn-danger remove-data"><i class="fa fa-trash"></i> Hapus</a>
            ';

            $rows[] = [
                'no'           => $no++,
                'foto'         => $fotoHtml,
                'nama_layanan' => esc($item['nama_layanan']),
                'deskripsi'    => esc($item['deskripsi']),
                'lokasi'       => $lokasiText,
                'jam_layanan'  => esc($item['jam_layanan']),
                'action'       => $action
            ];
        }

        return $this->response->setJSON([
            'draw'            => (int)($request->getGet('draw') ?? 1),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $rows
        ]);
    }

    public function layananGet($id)
    {
        $data = $this->layananModel->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
    }

    public function layananSave()
    {
        $id           = $this->request->getPost('id');
        $nama_layanan = $this->request->getPost('nama_layanan');
        $deskripsi    = $this->request->getPost('deskripsi');
        $location_id  = $this->request->getPost('location_id');
        $jam_layanan  = $this->request->getPost('jam_layanan');

        $data = [
            'nama_layanan' => $nama_layanan,
            'deskripsi'    => $deskripsi,
            'location_id'  => !empty($location_id) ? $location_id : null,
            'jam_layanan'  => $jam_layanan,
            'updated_by'   => session()->get('user_id') ?? 1
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $uploadPath = FCPATH . 'uploads/layanan';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Jika edit dan ada foto baru, hapus foto lama dari disk
            if (!empty($id)) {
                $oldData = $this->layananModel->find($id);
                if (!empty($oldData['foto']) && file_exists(FCPATH . 'uploads/layanan/' . $oldData['foto'])) {
                    @unlink(FCPATH . 'uploads/layanan/' . $oldData['foto']);
                }
            }

            $foto->move($uploadPath, $newName);
            $data['foto'] = $newName;
        }

        if (!empty($id)) {
            $this->layananModel->update($id, $data);
            $msg = 'Layanan berhasil diperbarui.';
        } else {
            $data['created_by'] = session()->get('user_id') ?? 1;
            $this->layananModel->insert($data);
            $msg = 'Layanan berhasil ditambahkan.';
        }

        session()->setFlashdata('swal_icon', 'success');
        session()->setFlashdata('swal_title', 'Berhasil');
        session()->setFlashdata('swal_text', $msg);

        return redirect()->to(base_url('cms/profil'));
    }

    public function layananDelete($id)
    {
        $data = $this->layananModel->find($id);
        if ($data) {
            // Hapus file gambar dari folder uploads/layanan jika ada
            if (!empty($data['foto']) && file_exists(FCPATH . 'uploads/layanan/' . $data['foto'])) {
                @unlink(FCPATH . 'uploads/layanan/' . $data['foto']);
            }
            $this->layananModel->delete($id);
        }

        session()->setFlashdata('swal_icon', 'success');
        session()->setFlashdata('swal_title', 'Berhasil');
        session()->setFlashdata('swal_text', 'Layanan berhasil dihapus.');

        return redirect()->to(base_url('cms/profil'));
    }
}
