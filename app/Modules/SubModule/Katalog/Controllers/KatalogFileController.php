<?php

namespace Katalog\Controllers;

/**
 * KatalogFileController
 * 
 * Menangani tampilan dan dekripsi file PDF yang terlampir
 * pada katalog, termasuk file artikel serial.
 */
class KatalogFileController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // VIEW - DECRYPT (Katalog)
    // ----------------------------------------------------------------

    public function view_decrypted($ID)
    {
        
        $id = decData($ID);
        session()->set('one_key', $id);

        $file = $this->fileModel->find($id);
      
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        if (!$this->canReadCatalogFile($file)) {
            session()->remove('one_key');
            return redirect()
                ->to(base_url('opac/detail/' . $file->Catalog_id))
                ->with('digital_error', 'Koleksi digital ini hanya bisa dibaca selama masa peminjaman aktif. Silakan pinjam melalui tombol Baca PDF.');
        }

        return view('Katalog\Views\slug\pdf_viewer', [
            'fileId'   => $id,
            'fileName' => $file->FileURL,
        ]);
    }

    public function get_decrypted_content($ID)
    {
        $oneKey = session()->get('one_key');
        session()->remove('one_key');

        if (!isset($_SERVER['HTTP_REFERER'])) {
            dd('Not found');
        }

        $id         = decData($ID);
        $currentURL = base_url('katalog/view_decrypted/');
        $idUrl      = decData(str_replace($currentURL, '', $_SERVER['HTTP_REFERER']));

        if ($idUrl != $id || !$oneKey || $oneKey !== $id) {
            dd('Not found');
        }

        $file = $this->fileModel->find($id);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        if (!$this->canReadCatalogFile($file)) {
            return $this->response->setStatusCode(403)->setBody('Peminjaman digital tidak aktif');
        }

        return $this->_streamDecryptedPdf(
            $this->modulePath . $file->FileURL,
            $file->FileURL
        );
    }

    /**
     * Akun yang terhubung ke anggota hanya boleh membuka file katalog OPAC, dan bila katalog punya
     * eksemplar DRM harus ada pinjaman aktif miliknya, sama seperti Opac::bacaDigital(). Staf tidak dibatasi.
     */
    private function canReadCatalogFile($file): bool
    {
        $db   = db_connect();
        $user = $db->table('users')->select('username')->where('id', login_id())->get()->getRow();
        $member = $user ? $db->table('members')->select('ID')->where('MemberNo', $user->username)->get()->getRow() : null;
        if (!$member) {
            return true;
        }

        $catalog = $db->table('catalogs')->select('IsOPAC')->where('ID', $file->Catalog_id)->get()->getRow();
        if (!$catalog || (int) $catalog->IsOPAC !== 1) {
            return false;
        }

        $collectionIds = array_column(
            $db->table('collections')->select('ID')->where('Catalog_id', $file->Catalog_id)->where('ISDRM', 1)->get()->getResultArray(),
            'ID'
        );
        if (!$collectionIds) {
            return true;
        }

        return $db->table('collectionloanitems')
            ->whereIn('Collection_id', $collectionIds)
            ->where('member_id', $member->ID)
            ->where('LoanStatus', 'Loan')
            ->where('DueDate >=', date('Y-m-d H:i:s'))
            ->countAllResults() > 0;
    }

    // ----------------------------------------------------------------
    // VIEW - DECRYPT (Artikel)
    // ----------------------------------------------------------------

    public function view_decrypted_article($ID)
    {
        $file = $this->serialArticleFilesModel->find($ID);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return view('Katalog\Views\slug\pdf_viewer_article', [
            'fileId'   => $ID,
            'fileName' => $file->FileURL,
        ]);
    }

    public function get_decrypted_content_article($ID)
    {
        $file = $this->serialArticleFilesModel->find($ID);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->_streamDecryptedPdf(
            $this->modulePath . $file->FileURL,
            $file->FileURL
        );
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPER
    // ----------------------------------------------------------------

    /**
     * Dekripsi file dan stream sebagai PDF inline.
     *
     * @param string $sourcePath Path file terenkripsi
     * @param string $fileName   Nama file untuk header Content-Disposition
     */
    private function _streamDecryptedPdf(string $sourcePath, string $fileName)
    {
        $tempDecryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');

        $encryption = new \App\Libraries\Encryption();
        $encryption->decryptFile($sourcePath, $tempDecryptedFile);

        $content = file_get_contents($tempDecryptedFile);
        unlink($tempDecryptedFile);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setHeader('Content-Security-Policy', "default-src 'self'; object-src 'self'")
            ->setBody($content);
    }
}