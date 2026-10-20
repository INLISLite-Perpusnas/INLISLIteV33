<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanFrekuensiExport
{
    public static function groups(string $report): array
    {
        $groups = [
            'Anggota' => [
                'members.MemberNo' => ['Nomor Anggota', 'members.MemberNo'],
                'members.Fullname' => ['Nama Lengkap', 'members.Fullname'],
                'members.PlaceOfBirth' => ['Tempat Lahir', 'members.PlaceOfBirth'],
                'members.DateOfBirth' => ['Tanggal Lahir', 'DATE(members.DateOfBirth)'],
                'members.Address' => ['Alamat', 'members.Address'],
                'members.Phone' => ['Telepon', 'members.Phone'],
                'members.Email' => ['Email', 'members.Email'],
                'members.Province' => ['Provinsi', 'members.Province'],
                'members.City' => ['Kota', 'members.City'],
                'members.Kecamatan' => ['Kecamatan', 'members.Kecamatan'],
                'members.Kelurahan' => ['Kelurahan', 'members.Kelurahan'],
                'members.RT' => ['RT', 'members.RT'],
                'members.RW' => ['RW', 'members.RW'],
                'members.InstitutionName' => ['Nama Institusi', 'members.InstitutionName'],
                'members.IdentityNo' => ['Nomor Identitas', 'members.IdentityNo'],
                'members.RegisterDate' => ['Tanggal Registrasi', 'DATE(members.RegisterDate)'],
                'members.EndDate' => ['Tanggal Berakhir', 'DATE(members.EndDate)'],
                'CreateBy' => ['Dibuat Oleh', 'creator.username'],
                'UpdateBy' => ['Diperbarui Oleh', 'updater.username'],
                'jenis_kelamin.Name' => ['Jenis Kelamin', 'jenis_kelamin.Name'],
                'jenis_anggota.jenisanggota' => ['Jenis Anggota', 'jenis_anggota.jenisanggota'],
            ],
            'BacaDitempat' => [
                'no_pengunjung' => ['Nomor Pengunjung', 'bacaditempat.NoPengunjung'],
                'lokasi' => ['Lokasi Perpustakaan', 'location_library.Name'],
                'lok_ruang' => ['Lokasi Ruang', 'locations.Name'],
                'periode' => ['Tanggal Kunjungan', 'DATE(bacaditempat.CreateDate)'],
                'nama' => ['Nama', 'CASE WHEN bacaditempat.Member_id IS NULL THEN bacaditempat.NoPengunjung ELSE members.FullName END'],
                'noinduk' => ['No. Induk', 'collections.NoInduk'],
                'noanggota' => ['No. Anggota', 'members.MemberNo'],
                'judul_katalog' => ['Judul', 'catalogs.Title'],
                'pengarang_katalog' => ['Pengarang', 'catalogs.Author'],
                'penerbit_katalog' => ['Penerbit', 'catalogs.Publisher'],
                'subjek_katalog' => ['Subjek', 'catalogs.Subject'],
                'nomor_dewey' => ['Nomor Dewey', 'catalogs.DeweyNo'],
            ],
            'BukuTamu' => [
                'no_pengunjung' => ['Nomor Kunjungan', 'no_pengunjung'],
                'lokasi' => ['Lokasi Perpustakaan', 'lokasi'],
                'lok_ruang' => ['Lokasi Ruang', 'lok_ruang'],
                'tgl_kunjungan' => ['Tanggal Kunjungan', 'tgl_kunjungan'],
                'ket' => ['Kriteria Pengunjung', 'ket'],
                'nama' => ['Nama', 'nama'],
                'gender' => ['Jenis Kelamin', 'gender'],
                'pekerjaan' => ['Pekerjaan', 'pekerjaan'],
                'pendidikan' => ['Pendidikan', 'pendidikan'],
                'tujuan' => ['Tujuan Kunjungan', 'tujuan'],
                'info' => ['Informasi Dicari', 'info'],
            ],
            'Eksemplar' => [
                'NomorBarcode' => ['No. Barcode', 'collections.NomorBarcode'],
                'TanggalPengadaan' => ['Tanggal Pengadaan', 'DATE(collections.TanggalPengadaan)'],
                'NoInduk' => ['No. Induk', 'collections.NoInduk'],
                'Title' => ['Judul', 'catalogs.Title'],
                'Author' => ['Pengarang', 'catalogs.Author'],
                'Edition' => ['Edisi', 'catalogs.Edition'],
                'Publisher' => ['Penerbit', 'catalogs.Publisher'],
                'PublishLocation' => ['Tempat Terbit', 'catalogs.PublishLocation'],
                'PublishYear' => ['Tahun Terbit', 'catalogs.PublishYear'],
                'Subject' => ['Subjek', 'catalogs.Subject'],
                'ISBN' => ['ISBN', 'catalogs.ISBN'],
                'CallNumber' => ['No. Panggil', 'collections.CallNumber'],
                'Languages' => ['Bahasa', 'catalogs.Languages'],
                'DeweyNo' => ['No. Dewey', 'catalogs.DeweyNo'],
                'RFID' => ['No. RFID', 'collections.RFID'],
                'JenisSumber' => ['Jenis Sumber', 'sources.JenisSumber'],
                'BentukFisik' => ['Bentuk Fisik', 'medias.BentukFisik'],
                'Kategori' => ['Kategori', 'categories.Kategori'],
                'Akses' => ['Akses', 'rules.Akses'],
                'LokasiRuang' => ['Lokasi Ruang', 'locations.LokasiRuang'],
                'NamaSumber' => ['Nama Sumber', 'partners.NamaSumber'],
                'Ketersediaan' => ['Ketersediaan', 'status.Ketersediaan'],
                'IsOPAC' => ['Status OPAC', 'collections.IsOPAC'],
                'IsDRM' => ['Status DRM', 'collections.IsDRM'],
                'Currency' => ['Mata Uang', 'collections.Currency'],
                'Price' => ['Harga', 'collections.Price'],
                'PriceType' => ['Satuan Harga', 'collections.PriceType'],
                'Perpustakaan' => ['Lokasi Perpustakaan', 'libraries.Perpustakaan'],
                'CreateBy' => ['Dibuat Oleh', 'creator.username'],
                'CreateDate' => ['Tanggal Dibuat', 'DATE(collections.CreateDate)'],
                'UpdateBy' => ['Diperbarui Oleh', 'updater.username'],
                'UpdateDate' => ['Tanggal Diperbarui', 'DATE(collections.UpdateDate)'],
            ],
            'Katalog' => [
                'ControlNumber' => ['No. Kontrol', 'catalogs.ControlNumber'],
                'BIBID' => ['BIB ID', 'catalogs.BIBID'],
                'Title' => ['Judul', 'catalogs.Title'],
                'Author' => ['Pengarang', 'catalogs.Author'],
                'Edition' => ['Edisi', 'catalogs.Edition'],
                'Publisher' => ['Penerbit', 'catalogs.Publisher'],
                'PublishLocation' => ['Tempat Terbit', 'catalogs.PublishLocation'],
                'PublishYear' => ['Tahun Terbit', 'catalogs.PublishYear'],
                'Subject' => ['Subjek', 'catalogs.Subject'],
                'PhysicalDescription' => ['Deskripsi Fisik', 'catalogs.PhysicalDescription'],
                'ISBN' => ['ISBN', 'catalogs.ISBN'],
                'CallNumber' => ['No. Panggil', 'catalogs.CallNumber'],
                'Languages' => ['Bahasa', 'catalogs.Languages'],
                'DeweyNo' => ['Klas DDC', 'catalogs.DeweyNo'],
                'Note' => ['Abstrak', 'catalogs.Note'],
                'IsOPAC' => ['Status OPAC', 'catalogs.IsOPAC'],
                'IsBNI' => ['Status BNI', 'catalogs.IsBNI'],
                'IsKIN' => ['Status KIN', 'catalogs.IsKIN'],
                'IsRDA' => ['Status RDA', 'catalogs.IsRDA'],
                'CreateBy' => ['Dibuat Oleh', 'creator.username'],
                'CreateDate' => ['Tanggal Dibuat', 'DATE(catalogs.CreateDate)'],
                'UpdateBy' => ['Diperbarui Oleh', 'updater.username'],
                'UpdateDate' => ['Tanggal Diperbarui', 'DATE(catalogs.UpdateDate)'],
            ],
            'Sirkulasi' => [
                'nama_anggota' => ['Nama Anggota', 'm.Fullname'],
                'MemberNo' => ['Nomor Anggota', 'm.MemberNo'],
                'NomorBarcode' => ['Nomor Barcode', 'col.NomorBarcode'],
                'judul_buku' => ['Judul Buku', 'cat.Title'],
                'penerbit' => ['Penerbit', 'cat.Publisher'],
                'kelas_ddc' => ['Kelas DDC', 'cat.DeweyNo'],
                'subjek' => ['Subjek', 'cat.Subject'],
                'tanggal_peminjaman' => ['Tanggal Peminjaman', 'cli.LoanDate'],
                'tanggal_jatuh_tempo' => ['Tanggal Jatuh Tempo', 'cli.DueDate'],
                'tanggal_pengembalian' => ['Tanggal Pengembalian', 'cli.ActualReturn'],
                'petugas_peminjaman' => ['Petugas Peminjaman', 'u_loan.username'],
                'petugas_pengembalian' => ['Petugas Pengembalian', 'u_return.username'],
                'jenis_kelamin' => ['Jenis Kelamin', "CASE WHEN m.Sex_id = 1 THEN 'Laki-laki' WHEN m.Sex_id = 2 THEN 'Perempuan' ELSE 'Tidak Diketahui' END"],
                'status_peminjaman' => ['Status Peminjaman', 'cli.LoanStatus'],
            ],
        ];

        return $groups[$report] ?? [];
    }
    public static function selectedGroups(string $report, $requested): array
    {
        if (!is_array($requested) || !$requested) {
            return [];
        }

        $definitions = self::groups($report);
        $selected = [];
        foreach ($requested as $key) {
            if (is_string($key) && isset($definitions[$key])) {
                $selected[$key] = $definitions[$key];
            }
        }
        return $selected;
    }

    public static function criteriaGroups(string $report, $requested, $fields, array $mapping): array
    {
        $selected = [];
        $definitions = self::groups($report);
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (is_string($field) && isset($mapping[$field], $definitions[$mapping[$field]])) {
                    $key = $mapping[$field];
                    $selected[$key] = $definitions[$key];
                }
            }
        }

        return $selected ?: self::selectedGroups($report, $requested);
    }
    public static function groupedRows($builder, array $selected, string $countExpression, ?string $extraCountExpression = null): array
    {
        $expressions = [];
        foreach (array_values($selected) as $index => $definition) {
            $expressions[] = $definition[1];
            $builder->select($definition[1] . ' AS group_' . $index, false);
        }
        $builder->select($countExpression . ' AS jumlah_data', false);
        if ($extraCountExpression !== null) {
            $builder->select($extraCountExpression . ' AS jumlah_eksemplar', false);
        }
        $builder->groupBy($expressions, false);
        $builder->orderBy('jumlah_data', 'DESC');

        return $builder->get()->getResultArray();
    }

    public static function previewHtml(array $selected, array $rows, bool $withExemplars = false): string
    {
        $labels = array_column(array_values($selected), 0);
        $headers = array_merge($labels, [$withExemplars ? 'Jumlah Judul' : 'Jumlah Data']);
        if ($withExemplars) {
            $headers[] = 'Jumlah Eksemplar';
        }
        $html = '<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        $totals = ['jumlah_data' => 0, 'jumlah_eksemplar' => 0];
        foreach ($rows as $row) {
            $totals['jumlah_data'] += (int) $row['jumlah_data'];
            if ($withExemplars) {
                $totals['jumlah_eksemplar'] += (int) $row['jumlah_eksemplar'];
            }
        }
        foreach (array_slice($rows, 0, 100) as $row) {
            $html .= '<tr>';
            foreach ($labels as $index => $label) {
                $html .= '<td>' . htmlspecialchars((string) ($row['group_' . $index] ?? '(Kosong)'), ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '<td>' . (int) $row['jumlah_data'] . '</td>';
            if ($withExemplars) {
                $html .= '<td>' . (int) $row['jumlah_eksemplar'] . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody><tfoot><tr><th colspan="' . count($labels) . '">Total</th><th>' . $totals['jumlah_data'] . '</th>';
        if ($withExemplars) {
            $html .= '<th>' . $totals['jumlah_eksemplar'] . '</th>';
        }
        $html .= '</tr></tfoot></table></div>';
        if (count($rows) > 100) {
            $html .= '<small>Preview menampilkan 100 dari ' . count($rows) . ' kelompok. Total mencakup seluruh kelompok.</small>';
        }
        return $html;
    }
    public static function download(string $title, array $selected, array $rows, string $format, bool $withExemplars = false): void
    {
        $labels = array_column(array_values($selected), 0);
        $headers = array_merge(['No.'], $labels, [$withExemplars ? 'Jumlah Judul' : 'Jumlah Data']);
        if ($withExemplars) {
            $headers[] = 'Jumlah Eksemplar';
        }
        $totals = ['jumlah_data' => 0, 'jumlah_eksemplar' => 0];
        foreach ($rows as $row) {
            $totals['jumlah_data'] += (int) $row['jumlah_data'];
            if ($withExemplars) {
                $totals['jumlah_eksemplar'] += (int) $row['jumlah_eksemplar'];
            }
        }
        $filename = str_replace(' ', '_', $title) . '_Frekuensi_' . date('d-m-Y_His');

        if ($format === 'pdf') {
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
                . 'body{font-family:DejaVu Sans,sans-serif;font-size:10px}h2{text-align:center}'
                . 'table{border-collapse:collapse;width:100%}th,td{border:1px solid #bbb;padding:6px}'
                . 'th{background:#eee}td.number{text-align:right}tfoot{font-weight:bold}'
                . '</style></head><body><h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - Frekuensi</h2><table><thead><tr>';
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ($rows as $index => $row) {
                $html .= '<tr><td>' . ($index + 1) . '</td>';
                foreach ($labels as $groupIndex => $label) {
                    $html .= '<td>' . htmlspecialchars((string) ($row['group_' . $groupIndex] ?? '(Kosong)'), ENT_QUOTES, 'UTF-8') . '</td>';
                }
                $html .= '<td class="number">' . (int) $row['jumlah_data'] . '</td>';
                if ($withExemplars) {
                    $html .= '<td class="number">' . (int) $row['jumlah_eksemplar'] . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody><tfoot><tr><td colspan="' . (count($labels) + 1) . '">Total</td>'
                . '<td class="number">' . $totals['jumlah_data'] . '</td>';
            if ($withExemplars) {
                $html .= '<td class="number">' . $totals['jumlah_eksemplar'] . '</td>';
            }
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html . '</tr></tfoot></table></body></html>');
            $dompdf->setPaper('A4', count($headers) > 5 ? 'landscape' : 'portrait');
            $dompdf->render();
            $dompdf->stream($filename . '.pdf', ['Attachment' => true]);
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Frekuensi');
        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach ($rows as $index => $row) {
            $line = $index + 2;
            $sheet->setCellValue('A' . $line, $index + 1);
            foreach ($labels as $groupIndex => $label) {
                $column = Coordinate::stringFromColumnIndex($groupIndex + 2);
                $sheet->setCellValueExplicit($column . $line, (string) ($row['group_' . $groupIndex] ?? '(Kosong)'), DataType::TYPE_STRING);
            }
            $sheet->setCellValue(Coordinate::stringFromColumnIndex(count($labels) + 2) . $line, (int) $row['jumlah_data']);
            if ($withExemplars) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex(count($labels) + 3) . $line, (int) $row['jumlah_eksemplar']);
            }
        }
        $line = count($rows) + 2;
        $sheet->setCellValue('A' . $line, 'Total');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex(count($labels) + 2) . $line, $totals['jumlah_data']);
        if ($withExemplars) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex(count($labels) + 3) . $line, $totals['jumlah_eksemplar']);
        }
        $sheet->getStyle('A' . $line . ':' . Coordinate::stringFromColumnIndex(count($headers)) . $line)->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}