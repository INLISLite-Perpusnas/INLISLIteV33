<?php

namespace Eksemplar\Controllers;

/**
 * EksemplarLabelController
 *
 * Menangani cetak label eksemplar.
 */
class EksemplarLabelController extends \Base\Controllers\BaseController
{
    use EksemplarBase;

    function __construct()
    {
        $this->initEksemplarBase();
    }

    public function print_label()
    {
        helper(['thumbnail', 'form']);

        $this->data['title'] = 'Cetak Label Eksemplar';

        $this->validation->setRules([
            'eksemplar_ids' => ['label' => 'Eksemplar',      'rules' => 'required'],
            'eksemplar_tpl' => ['label' => 'Template Label', 'rules' => 'required'],
        ]);

        if (!$this->request->getPost() || !$this->validation->withRequest($this->request)->run()) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : 'Tidak ada eksemplar yang dipilih.'
            );
            return redirect()->back();
        }

        $post      = $this->request->getPost();
        $template  = $post['eksemplar_tpl'];
        $paperSize = $post['paper_size'] ?? 'a4';
        $idsArr    = array_filter(
            array_map('intval', explode(',', preg_replace('/[^0-9,]/', '', $post['eksemplar_ids']))),
            fn($id) => $id > 0
        );

        if (empty($idsArr)) {
            $this->session->setFlashdata('swal_icon',  'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_html',  'Tidak ada ID eksemplar yang valid.');
            return redirect()->back();
        }

        $allowedTemplates = [
            'cetak-label-a4-1', 'cetak-label-a4-2', 'cetak-label-a4-3',
            'cetak-label-a4-4', 'cetak-label-a4-5', 'cetak-label-a4-4-qrcode',
            'cetak-label-lr1',  'cetak-label-lr2',  'cetak-label-lr3',
            'cetak-label-lr4',  'cetak-label-lr5',  'cetak-label-lr6',
            'cetak-label-br1',  'cetak-label-br2',
            'cetak-label-tj107-1',
            'cetak-label-tj121-1', 'cetak-label-tj121-2',
            'cetak-label-gc121-1', 'cetak-label-gc121-2',
            'cetak-label-gc121-3', 'cetak-label-gc121-4',
        ];

        if (!in_array($template, $allowedTemplates, true)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Template tidak dikenali: ' . esc($template));
            return redirect()->back();
        }

        $db = db_connect();

        $eksemplarData = $db->table('collections as a')
            ->select('a.ID, a.NomorBarcode, b.Title, b.CallNumber')
            ->join('catalogs b', 'b.ID = a.Catalog_id')
            ->whereIn('a.ID', $idsArr)
            ->get()
            ->getResultObject();

        if (empty($eksemplarData)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Data eksemplar tidak ditemukan.');
            return redirect()->back();
        }

        $namaPerpustakaan = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Mitra';

        $useQrCode = str_contains($template, 'qrcode') || str_contains($paperSize, 'qrcode');

        $LabelData = [];
        foreach ($eksemplarData as $row) {
            $LabelData[] = [
                'Title'            => character_limiter($row->Title, 50),
                'Barcode'          => $row->NomorBarcode,
                'CallNumber'       => $row->CallNumber,
                'NamaPerpustakaan' => $namaPerpustakaan,
                'Warna1'           => '#FFFF66',
                'BarcodePNG'       => $useQrCode
                                        ? get_qrcode_png($row->NomorBarcode)
                                        : get_barcode_png($row->NomorBarcode),
            ];
        }

        return view('Eksemplar\Views\template\\' . $template, ['LabelData' => $LabelData]);
    }
}
