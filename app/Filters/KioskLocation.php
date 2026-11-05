<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use KiosBukuTamu\Libraries\KioskAuth;

/**
 * Layanan kios (baca di tempat, peminjaman dan pengembalian mandiri) hanya untuk
 * perangkat yang sudah diaktifkan dengan kode dari menu Aktivasi Kios.
 */
class KioskLocation implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (KioskAuth::requestLocationId($request) !== null) {
            return;
        }

        // Permintaan AJAX/fetch mengharapkan JSON, bukan halaman hasil redirect
        if ($request->isAJAX() || !str_contains($request->getHeaderLine('Accept'), 'text/html')) {
            return service('response')->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Perangkat ini belum diaktifkan sebagai kios.',
            ]);
        }

        return redirect()->to('buku-tamu/lokasi');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
