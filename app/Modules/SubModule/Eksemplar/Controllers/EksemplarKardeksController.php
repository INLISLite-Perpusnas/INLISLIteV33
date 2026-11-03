<?php

namespace Eksemplar\Controllers;

use App\Libraries\DataTable;

/**
 * EksemplarKardeksController
 *
 * Kardeks Terbitan Berkala: daftar katalog terbitan berkala (worksheets.ISSERIAL = 1),
 * dirinci per Edisi Serial lalu per eksemplar. Setara akuisisi/kardeks-terbitan-berkala
 * INLISLite 3 (CollectionSearchKardeks::advancedSearch, search2, search3).
 */
class EksemplarKardeksController extends \Base\Controllers\BaseController
{
    private function denied()
    {
        set_message('toastr_msg', 'Maaf, Anda tidak memiliki akses');
        set_message('toastr_type', 'error');
        return redirect()->to('dashboard');
    }

    /** Header X-Requested-With tidak dipercaya untuk izin (lihat is_allowed). */
    private function allowed(): bool
    {
        return is_allowed('kardeks-terbitan-berkala/index', null, false);
    }

    public function index()
    {
        if (!$this->allowed()) return $this->denied();

        return view('Eksemplar\Views\list_kardeks', [
            'title' => 'Kardeks Terbitan Berkala',
        ]);
    }

    /** Daftar katalog terbitan berkala + jumlah edisi serial dan eksemplar. */
    public function datatable()
    {
        if (!$this->allowed()) return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak']);
        $db = db_connect();

        // Library DataTable membaca kolom dari teks SELECT..FROM, jadi jangan pakai subquery di sini;
        // jumlah edisi & eksemplar diisi lewat callback dari satu query terkelompok.
        $builder = $db->table('catalogs c')
            ->select('c.ID, c.ID as expand, c.BIBID, c.Title, c.Author, c.PublishLocation, c.Publisher, c.PublishYear')
            ->select('c.ISBN, c.CallNumber, c.ControlNumber, c.ID as JumlahEdisiSerial, c.ID as Eksemplar')
            ->join('worksheets w', 'w.ID = c.Worksheet_id')
            ->where('w.ISSERIAL', 1)
            ->orderBy('c.CreateDate', 'DESC');

        $counts = null;
        $count = function (int $catalogId, string $field) use (&$counts, $db) {
            if ($counts === null) {
                $counts = [];
                $rows = $db->table('collections col')
                    ->select('col.Catalog_id, COUNT(*) AS Eksemplar')
                    ->select("COUNT(DISTINCT CASE WHEN col.EDISISERIAL IS NOT NULL AND col.EDISISERIAL <> '' THEN CONCAT(col.EDISISERIAL, '|', COALESCE(col.TANGGAL_TERBIT_EDISI_SERIAL, '')) END) AS JumlahEdisiSerial", false)
                    ->join('catalogs c', 'c.ID = col.Catalog_id')
                    ->join('worksheets w', 'w.ID = c.Worksheet_id')
                    ->where('w.ISSERIAL', 1)
                    ->groupBy('col.Catalog_id')
                    ->get()->getResultArray();
                foreach ($rows as $row) $counts[$row['Catalog_id']] = $row;
            }
            return (int) ($counts[$catalogId][$field] ?? 0);
        };

        return DataTable::of($builder)
            ->addNumbering('no')
            ->edit('JumlahEdisiSerial', fn($row) => $count((int) $row->ID, 'JumlahEdisiSerial'))
            ->edit('Eksemplar', fn($row) => $count((int) $row->ID, 'Eksemplar'))
            ->edit('expand', function ($row) {
                return '<button type="button" class="btn btn-sm btn-link p-0 btn-kardeks-expand" data-id="' . (int) $row->ID . '" title="Lihat edisi serial">'
                    . '<i class="fa fa-plus-square fa-lg"></i></button>';
            })
            ->edit('BIBID', fn($row) => esc($row->BIBID))
            ->edit('Title', fn($row) => '<a href="' . site_url('katalog/detail/' . $row->ID) . '">' . esc($row->Title) . '</a>')
            ->edit('Author', fn($row) => esc($row->Author))
            ->edit('PublishLocation', fn($row) => esc($row->PublishLocation))
            ->edit('Publisher', fn($row) => esc($row->Publisher))
            ->edit('PublishYear', fn($row) => esc($row->PublishYear))
            ->toJson();
    }

    /** Rincian per Edisi Serial beserta eksemplarnya (HTML untuk baris yang dibuka). */
    public function edisi(int $catalogId = 0)
    {
        if (!$this->allowed()) return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        $db = db_connect();
        $edisi = $db->table('collections')
            ->select('EDISISERIAL, TANGGAL_TERBIT_EDISI_SERIAL, COUNT(*) AS Eksemplar')
            ->where('Catalog_id', $catalogId)
            ->groupBy(['EDISISERIAL', 'TANGGAL_TERBIT_EDISI_SERIAL'])
            ->orderBy('EDISISERIAL', 'DESC')
            ->get()->getResultArray();

        $eksemplar = $db->table('collections col')
            ->select('col.EDISISERIAL, col.TANGGAL_TERBIT_EDISI_SERIAL, col.NomorBarcode, col.NoInduk, col.Price, col.TanggalPengadaan')
            ->select('src.Name as Source, med.Name as Media, cat.Name as Category')
            ->join('collectionsources src', 'src.ID = col.Source_id', 'left')
            ->join('collectionmedias med', 'med.ID = col.Media_id', 'left')
            ->join('collectioncategorys cat', 'cat.ID = col.Category_id', 'left')
            ->where('col.Catalog_id', $catalogId)
            ->orderBy('col.ID')
            ->get()->getResultArray();

        $byEdisi = [];
        foreach ($eksemplar as $row) {
            $byEdisi[$row['EDISISERIAL'] . '|' . $row['TANGGAL_TERBIT_EDISI_SERIAL']][] = $row;
        }

        return view('Eksemplar\Views\partial\kardeks_edisi', [
            'catalogId' => $catalogId,
            'edisi' => $edisi,
            'byEdisi' => $byEdisi,
        ]);
    }
}
