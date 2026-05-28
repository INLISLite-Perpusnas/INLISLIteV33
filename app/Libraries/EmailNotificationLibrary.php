<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

/**
 * EmailNotificationLibrary
 * 
 * Library untuk mengirim notifikasi email keterlambatan pengembalian buku.
 * Letakkan file ini di: app/Libraries/EmailNotificationLibrary.php
 */
class EmailNotificationLibrary
{
    protected Email $email;
    protected string $fromEmail;
    protected string $fromName;
    protected array  $smtpConfig;
    protected $settingModel;

    public function __construct()
    {
        $this->email     = \Config\Services::email();
        $this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->fromEmail = getenv('email.SMTPUser') ?: env('email.SMTPUser', 'library@example.com');
        $this->fromName  = getenv('email.fromName')  ?: env('email.fromName', 'Perpustakaan Digital');

        // Sama persis dengan pola member_notify() yang sudah terbukti bekerja
        $this->smtpConfig = [
            'protocol'   => 'smtp',
            'SMTPHost'   => getenv('email.SMTPHost')   ?: 'smtp.gmail.com',
            'SMTPUser'   => getenv('email.SMTPUser')   ?: '',
            'SMTPPass'   => getenv('email.SMTPPass')   ?: '',
            'SMTPPort'   => (int)(getenv('email.SMTPPort') ?: 587),
            'SMTPCrypto' => getenv('email.SMTPCrypto') ?: 'tls',
            'mailType'   => 'html',
            'charset'    => 'utf-8',
            'newline'    => "\r\n",
        ];
    }

    /**
     * Kirim notifikasi keterlambatan ke SATU anggota.
     *
     * @param  object  $loanData   Row data dari query (stdClass)
     * @param  int     $lateDays   Jumlah hari terlambat (sudah dihitung tanpa hari libur)
     * @return array   ['success' => bool, 'message' => string]
     */
    public function sendOverdueNotification(object $loanData, int $lateDays): array
    {
        if (empty($loanData->Email)) {
            return ['success' => false, 'message' => 'Email anggota tidak ditemukan.'];
        }

        $this->email->initialize($this->smtpConfig); // wajib, seperti member_notify()
        $this->email->clear();
        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($loanData->Email);
        $this->email->setSubject('Notifikasi Keterlambatan Pengembalian Buku');
        $this->email->setMessage($this->buildSingleTemplate($loanData, $lateDays));

        try {
            $sent = $this->email->send();
            log_message('info', '[EmailNotif] Send to: ' . $loanData->Email . ' - ' . ($sent ? 'OK' : 'FAILED'));

            if ($sent) {
                return ['success' => true, 'message' => 'Email berhasil dikirim ke ' . $loanData->Email];
            }

            $debug = $this->email->printDebugger(['headers']);
            log_message('error', '[EmailNotif] Failed: ' . $debug);
            return ['success' => false, 'message' => 'Gagal kirim ke ' . $loanData->Email . '. Cek log untuk detail.'];

        } catch (\Exception $e) {
            log_message('error', '[EmailNotif] Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Kirim notifikasi massal ke BANYAK anggota sekaligus.
     * Setiap anggota mendapat satu email yang merangkum semua pinjamannya yang terlambat.
     *
     * @param  array  $groupedLoans  Array berformat: [ email => [ loans ] ]
     *                               Tiap loan memiliki property: Fullname, MemberNo, Title,
     *                               NomorBarcode, DueDate, LateDays
     * @return array  ['sent' => int, 'failed' => int, 'errors' => []]
     */
    public function sendBulkOverdueNotification(array $groupedLoans): array
    {
        $result = ['sent' => 0, 'failed' => 0, 'errors' => []];

        foreach ($groupedLoans as $email => $loans) {
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['failed']++;
                $result['errors'][] = "Email tidak valid: '{$email}'";
                continue;
            }

            $this->email->initialize($this->smtpConfig); // reinit tiap iterasi
            $this->email->clear();
            $this->email->setFrom($this->fromEmail, $this->fromName);
            $this->email->setTo($email);
            $this->email->setSubject('Notifikasi Keterlambatan Pengembalian Buku');
            $this->email->setMessage($this->buildBulkTemplate($loans));

            try {
                $sent = $this->email->send();
                log_message('info', '[EmailNotif] Bulk send to: ' . $email . ' - ' . ($sent ? 'OK' : 'FAILED'));

                if ($sent) {
                    $result['sent']++;
                } else {
                    $result['failed']++;
                    $debug = $this->email->printDebugger(['headers']);
                    log_message('error', '[EmailNotif] Bulk failed ' . $email . ': ' . $debug);
                    $result['errors'][] = "Gagal kirim ke {$email}";
                }
            } catch (\Exception $e) {
                $result['failed']++;
                log_message('error', '[EmailNotif] Bulk exception ' . $email . ': ' . $e->getMessage());
                $result['errors'][] = "Error kirim ke {$email}: " . $e->getMessage();
            }
        }

        return $result;
    }

    /**
     * Kirim struk peminjaman ke email anggota.
     *
     * @param  object  $loan       Row data loan (harus punya Email, Fullname, MemberNo, ID, CreateDate)
     * @param  array   $loanItems  Array loan items (Title, NomorBarcode, CallNumber, DueDate)
     * @return array   ['success' => bool, 'message' => string]
     */
    public function sendStrukEmail(object $loan, array $loanItems): array
    {
        if (empty($loan->Email)) {
            return ['success' => false, 'message' => 'Email anggota tidak ditemukan.'];
        }

        if (!filter_var($loan->Email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid: ' . $loan->Email];
        }

        $this->email->initialize($this->smtpConfig);
        $this->email->clear();
        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($loan->Email);
        $this->email->setSubject('Struk Peminjaman Buku - ID #' . $loan->ID);
        $this->email->setMessage($this->buildStrukTemplate($loan, $loanItems));

        try {
            $sent = $this->email->send();
            log_message('info', '[EmailNotif] Struk to: ' . $loan->Email . ' - ' . ($sent ? 'OK' : 'FAILED'));

            if ($sent) {
                return ['success' => true, 'message' => 'Struk berhasil dikirim ke ' . $loan->Email];
            }

            $debug = $this->email->printDebugger(['headers']);
            log_message('error', '[EmailNotif] Struk failed: ' . $debug);
            return ['success' => false, 'message' => 'Gagal mengirim struk ke ' . $loan->Email . '. Cek log untuk detail.'];

        } catch (\Exception $e) {
            log_message('error', '[EmailNotif] Struk exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================================================================
    // PRIVATE: Template Builder
    // =========================================================================

    /**
     * Template HTML untuk notifikasi satu buku.
     */
    private function buildSingleTemplate(object $loan, int $lateDays): string
    {
        $appName = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $dueDate = date('d F Y', strtotime($loan->DueDate));

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Notifikasi Keterlambatan</title>
        </head>
        <body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                            <!-- HEADER -->
                            <tr>
                                <td style="background:linear-gradient(135deg,#1a3a5c,#2563eb);padding:32px 40px;text-align:center;">
                                    <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:0.5px;">
                                        📚 {$appName}
                                    </h1>
                                    <p style="margin:8px 0 0;color:#bfdbfe;font-size:13px;">Sistem Informasi Perpustakaan</p>
                                </td>
                            </tr>

                            <!-- ALERT BANNER -->
                            <tr>
                                <td style="background:#fef2f2;border-left:4px solid #ef4444;padding:16px 40px;">
                                    <p style="margin:0;color:#b91c1c;font-size:14px;font-weight:600;">
                                        ⚠️ Buku Anda telah melewati tanggal jatuh tempo pengembalian.
                                    </p>
                                </td>
                            </tr>

                            <!-- BODY -->
                            <tr>
                                <td style="padding:32px 40px;">
                                    <p style="margin:0 0 8px;color:#374151;font-size:15px;">
                                        Kepada Yth. <strong>{$loan->Fullname}</strong> ({$loan->MemberNo}),
                                    </p>
                                    <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">
                                        Kami menginformasikan bahwa Anda memiliki buku yang <strong>belum dikembalikan</strong>
                                        dan telah melewati batas waktu pengembalian.
                                    </p>

                                    <!-- BOOK INFO CARD -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                        <tr>
                                            <td style="background:#1e40af;padding:12px 20px;">
                                                <p style="margin:0;color:#ffffff;font-size:13px;font-weight:600;">📖 Detail Pinjaman</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:20px;">
                                                <table width="100%" cellpadding="4" cellspacing="0">
                                                    <tr>
                                                        <td style="color:#6b7280;font-size:13px;width:140px;">No. Transaksi</td>
                                                        <td style="color:#374151;font-size:13px;font-weight:600;">: {$loan->CollectionLoan_id}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#6b7280;font-size:13px;">No. Barcode</td>
                                                        <td style="color:#374151;font-size:13px;font-weight:600;">: {$loan->NomorBarcode}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#6b7280;font-size:13px;">Judul Buku</td>
                                                        <td style="color:#374151;font-size:13px;font-weight:600;">: {$loan->Title}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#6b7280;font-size:13px;">Penerbit</td>
                                                        <td style="color:#374151;font-size:13px;">: {$loan->Publisher}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#6b7280;font-size:13px;">Jatuh Tempo</td>
                                                        <td style="color:#ef4444;font-size:13px;font-weight:700;">: {$dueDate}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- LATE DAYS BADGE -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="background:linear-gradient(135deg,#fef2f2,#fff1f2);border:1px solid #fca5a5;border-radius:8px;margin-bottom:24px;">
                                        <tr>
                                            <td style="padding:20px;text-align:center;">
                                                <p style="margin:0;color:#6b7280;font-size:13px;">Total Hari Terlambat (tidak termasuk hari libur)</p>
                                                <p style="margin:8px 0 0;color:#dc2626;font-size:42px;font-weight:800;line-height:1;">
                                                    {$lateDays}
                                                </p>
                                                <p style="margin:4px 0 0;color:#dc2626;font-size:14px;font-weight:600;">Hari</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.7;">
                                        Mohon segera kembalikan buku tersebut ke perpustakaan untuk menghindari
                                        penambahan denda. Jika sudah dikembalikan, abaikan pesan ini.
                                    </p>
                                </td>
                            </tr>

                            <!-- FOOTER -->
                            <tr>
                                <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
                                    <p style="margin:0;color:#9ca3af;font-size:12px;">
                                        Email ini dikirim secara otomatis oleh sistem {$appName}.<br>
                                        Harap tidak membalas email ini.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }

    /**
     * Template HTML untuk notifikasi massal (banyak buku dalam satu email).
     *
     * @param  array  $loans  Array of loan objects untuk satu anggota
     */
    private function buildBulkTemplate(array $loans): string
    {
        $appName    = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $firstLoan  = $loans[0];
        $memberName = $firstLoan->Fullname;
        $memberNo   = $firstLoan->MemberNo;
        $totalBooks = count($loans);

        // Build rows for each overdue book
        $bookRows = '';
        foreach ($loans as $i => $loan) {
            $dueDate  = date('d F Y', strtotime($loan->DueDate));
            $rowBg    = ($i % 2 === 0) ? '#f8fafc' : '#ffffff';
            $bookRows .= <<<ROW
            <tr style="background:{$rowBg};">
                <td style="padding:10px 16px;color:#374151;font-size:13px;border-bottom:1px solid #f1f5f9;">
                    <strong>{$loan->Title}</strong><br>
                    <span style="color:#9ca3af;font-size:11px;">{$loan->NomorBarcode} &bull; {$loan->Publisher}</span>
                </td>
                <td style="padding:10px 16px;color:#ef4444;font-size:13px;font-weight:600;text-align:center;border-bottom:1px solid #f1f5f9;white-space:nowrap;">
                    {$dueDate}
                </td>
                <td style="padding:10px 16px;font-size:13px;text-align:center;border-bottom:1px solid #f1f5f9;white-space:nowrap;">
                    <span style="background:#fef2f2;color:#dc2626;font-weight:700;padding:3px 10px;border-radius:20px;font-size:12px;">
                        +{$loan->LateDays} hari
                    </span>
                </td>
            </tr>
            ROW;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                            <!-- HEADER -->
                            <tr>
                                <td style="background:linear-gradient(135deg,#1a3a5c,#2563eb);padding:32px 40px;text-align:center;">
                                    <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">
                                        📚 {$appName}
                                    </h1>
                                    <p style="margin:8px 0 0;color:#bfdbfe;font-size:13px;">Sistem Informasi Perpustakaan</p>
                                </td>
                            </tr>

                            <!-- ALERT BANNER -->
                            <tr>
                                <td style="background:#fef2f2;border-left:4px solid #ef4444;padding:16px 40px;">
                                    <p style="margin:0;color:#b91c1c;font-size:14px;font-weight:600;">
                                        ⚠️ Anda memiliki <strong>{$totalBooks} buku</strong> yang melewati tanggal jatuh tempo pengembalian.
                                    </p>
                                </td>
                            </tr>

                            <!-- BODY -->
                            <tr>
                                <td style="padding:32px 40px 16px;">
                                    <p style="margin:0 0 8px;color:#374151;font-size:15px;">
                                        Kepada Yth. <strong>{$memberName}</strong> ({$memberNo}),
                                    </p>
                                    <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">
                                        Berikut adalah daftar buku yang <strong>belum dikembalikan</strong>
                                        dan telah melewati batas waktu pengembalian:
                                    </p>

                                    <!-- BOOKS TABLE -->
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                        <tr style="background:#1e40af;">
                                            <th style="padding:10px 16px;color:#ffffff;font-size:12px;text-align:left;font-weight:600;">Judul Buku</th>
                                            <th style="padding:10px 16px;color:#ffffff;font-size:12px;text-align:center;font-weight:600;">Jatuh Tempo</th>
                                            <th style="padding:10px 16px;color:#ffffff;font-size:12px;text-align:center;font-weight:600;">Terlambat</th>
                                        </tr>
                                        {$bookRows}
                                    </table>

                                    <p style="margin:0 0 24px;color:#6b7280;font-size:13px;line-height:1.7;">
                                        Mohon segera kembalikan buku-buku tersebut ke perpustakaan untuk menghindari
                                        penambahan denda. Jika sudah dikembalikan, abaikan pesan ini.
                                    </p>
                                </td>
                            </tr>

                            <!-- FOOTER -->
                            <tr>
                                <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
                                    <p style="margin:0;color:#9ca3af;font-size:12px;">
                                        Email ini dikirim secara otomatis oleh sistem {$appName}.<br>
                                        Harap tidak membalas email ini.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }

    /**
     * Kirim struk pengembalian ke email anggota.
     *
     * @param  object  $member   Data anggota (Email, Fullname, MemberNo)
     * @param  array   $items    Array items yang dikembalikan (Title, NomorBarcode, DueDate, LateDays)
     * @return array   ['success' => bool, 'message' => string]
     */
    public function sendStrukPengembalianEmail(object $member, array $items): array
    {
        if (empty($member->Email)) {
            return ['success' => false, 'message' => 'Email anggota tidak ditemukan.'];
        }

        if (!filter_var($member->Email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid: ' . $member->Email];
        }

        $this->email->initialize($this->smtpConfig);
        $this->email->clear();
        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($member->Email);
        $this->email->setSubject('Struk Pengembalian Buku - ' . date('d/m/Y H:i'));
        $this->email->setMessage($this->buildStrukPengembalianTemplate($member, $items));

        try {
            $sent = $this->email->send();
            log_message('info', '[EmailNotif] Struk pengembalian to: ' . $member->Email . ' - ' . ($sent ? 'OK' : 'FAILED'));

            if ($sent) {
                return ['success' => true, 'message' => 'Struk pengembalian berhasil dikirim ke ' . $member->Email];
            }

            $debug = $this->email->printDebugger(['headers']);
            log_message('error', '[EmailNotif] Struk pengembalian failed: ' . $debug);
            return ['success' => false, 'message' => 'Gagal mengirim struk ke ' . $member->Email . '. Cek log untuk detail.'];

        } catch (\Exception $e) {
            log_message('error', '[EmailNotif] Struk pengembalian exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Kirim struk perpanjangan ke email anggota.
     */
    public function sendStrukPerpanjanganEmail(object $member, array $items): array
    {
        if (empty($member->Email)) {
            return ['success' => false, 'message' => 'Email anggota tidak ditemukan.'];
        }

        if (!filter_var($member->Email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid: ' . $member->Email];
        }

        $this->email->initialize($this->smtpConfig);
        $this->email->clear();
        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($member->Email);
        $this->email->setSubject('Struk Perpanjangan Buku - ' . date('d/m/Y H:i'));
        $this->email->setMessage($this->buildStrukPerpanjanganTemplate($member, $items));

        try {
            $sent = $this->email->send();
            log_message('info', '[EmailNotif] Struk perpanjangan to: ' . $member->Email . ' - ' . ($sent ? 'OK' : 'FAILED'));

            if ($sent) {
                return ['success' => true, 'message' => 'Struk perpanjangan berhasil dikirim ke ' . $member->Email];
            }

            $debug = $this->email->printDebugger(['headers']);
            log_message('error', '[EmailNotif] Struk perpanjangan failed: ' . $debug);
            return ['success' => false, 'message' => 'Gagal mengirim struk ke ' . $member->Email . '. Cek log untuk detail.'];

        } catch (\Exception $e) {
            log_message('error', '[EmailNotif] Struk perpanjangan exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function buildStrukPerpanjanganTemplate(object $member, array $items): string
    {
        $appName    =  $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $extendDate = date('d F Y H:i');
        $totalBooks = count($items);

        $bookRows = '';
        foreach ($items as $i => $item) {
            $rowBg      = ($i % 2 === 0) ? '#f8fafc' : '#ffffff';
            $newDueDate = date('d F Y', strtotime($item->DueDateExtend));
            $bookRows  .= <<<ROW
            <tr style="background:{$rowBg};">
                <td style="padding:10px 16px;color:#374151;font-size:13px;border-bottom:1px solid #f1f5f9;">
                    <strong>{$item->Title}</strong><br>
                    <span style="color:#9ca3af;font-size:11px;">{$item->NomorBarcode}</span>
                </td>
                <td style="padding:10px 16px;color:#6b7280;font-size:12px;text-align:center;border-bottom:1px solid #f1f5f9;">{$item->CallNumber}</td>
                <td style="padding:10px 16px;color:#1e40af;font-size:12px;font-weight:700;text-align:center;border-bottom:1px solid #f1f5f9;white-space:nowrap;">{$newDueDate}</td>
            </tr>
            ROW;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
                <tr><td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,#0f2356,#1B3878);padding:32px 40px;text-align:center;">
                                <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">📚 {$appName}</h1>
                                <p style="margin:8px 0 0;color:#bfdbfe;font-size:13px;">Struk Perpanjangan Buku</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#eff6ff;border-left:4px solid #3b82f6;padding:16px 40px;">
                                <p style="margin:0;color:#1d4ed8;font-size:14px;font-weight:600;">🔄 Perpanjangan berhasil diproses pada {$extendDate}.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px 40px;">
                                <p style="margin:0 0 8px;color:#374151;font-size:15px;">Kepada Yth. <strong>{$member->Fullname}</strong> ({$member->MemberNo}),</p>
                                <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">Masa pinjam buku Anda telah berhasil diperpanjang. Berikut detail perpanjangan.</p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr><td style="background:#1e40af;padding:12px 20px;"><p style="margin:0;color:#ffffff;font-size:13px;font-weight:600;">📋 Informasi Perpanjangan</p></td></tr>
                                    <tr><td style="padding:20px;">
                                        <table width="100%" cellpadding="4" cellspacing="0">
                                            <tr><td style="color:#6b7280;font-size:13px;width:140px;">Nomor Anggota</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$member->MemberNo}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Tanggal Perpanjangan</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$extendDate}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Jumlah Buku</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$totalBooks} buku</td></tr>
                                        </table>
                                    </td></tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr style="background:#1e40af;">
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:left;font-weight:600;">Judul Buku</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">No. Panggil</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">Jatuh Tempo Baru</th>
                                    </tr>
                                    {$bookRows}
                                </table>
                                <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.7;">Harap kembalikan buku sebelum tanggal jatuh tempo baru untuk menghindari denda.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
                                <p style="margin:0;color:#9ca3af;font-size:12px;">Email ini dikirim otomatis oleh sistem {$appName}.<br>Harap tidak membalas email ini.</p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>
        HTML;
    }

    private function buildStrukPengembalianTemplate(object $member, array $items): string
    {
        $appName    = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $returnDate = date('d F Y H:i');
        $totalBooks = count($items);

        $bookRows = '';
        foreach ($items as $i => $item) {
            $rowBg    = ($i % 2 === 0) ? '#f8fafc' : '#ffffff';
            $lateDays = (int)($item->LateDays ?? 0);
            $dueDate  = date('d F Y', strtotime($item->DueDate));
            $statusHtml = $lateDays > 0
                ? "<span style=\"background:#fef2f2;color:#dc2626;font-weight:700;padding:3px 10px;border-radius:20px;font-size:12px;\">+{$lateDays} hari terlambat</span>"
                : "<span style=\"background:#f0fdf4;color:#16a34a;font-weight:700;padding:3px 10px;border-radius:20px;font-size:12px;\">Tepat Waktu</span>";

            $bookRows .= <<<ROW
            <tr style="background:{$rowBg};">
                <td style="padding:10px 16px;color:#374151;font-size:13px;border-bottom:1px solid #f1f5f9;">
                    <strong>{$item->Title}</strong><br>
                    <span style="color:#9ca3af;font-size:11px;">{$item->NomorBarcode}</span>
                </td>
                <td style="padding:10px 16px;color:#6b7280;font-size:12px;text-align:center;border-bottom:1px solid #f1f5f9;white-space:nowrap;">{$dueDate}</td>
                <td style="padding:10px 16px;text-align:center;border-bottom:1px solid #f1f5f9;">{$statusHtml}</td>
            </tr>
            ROW;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
                <tr><td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,#0f2356,#1B3878);padding:32px 40px;text-align:center;">
                                <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">📚 {$appName}</h1>
                                <p style="margin:8px 0 0;color:#bfdbfe;font-size:13px;">Struk Pengembalian Buku</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f0fdf4;border-left:4px solid #22c55e;padding:16px 40px;">
                                <p style="margin:0;color:#15803d;font-size:14px;font-weight:600;">✅ Pengembalian berhasil diproses pada {$returnDate}.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px 40px;">
                                <p style="margin:0 0 8px;color:#374151;font-size:15px;">Kepada Yth. <strong>{$member->Fullname}</strong> ({$member->MemberNo}),</p>
                                <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">Terima kasih telah mengembalikan buku tepat waktu. Berikut struk pengembalian Anda.</p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr><td style="background:#1e40af;padding:12px 20px;"><p style="margin:0;color:#ffffff;font-size:13px;font-weight:600;">📋 Ringkasan Pengembalian</p></td></tr>
                                    <tr><td style="padding:20px;">
                                        <table width="100%" cellpadding="4" cellspacing="0">
                                            <tr><td style="color:#6b7280;font-size:13px;width:140px;">Nomor Anggota</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$member->MemberNo}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Tanggal Kembali</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$returnDate}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Jumlah Buku</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$totalBooks} buku</td></tr>
                                        </table>
                                    </td></tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr style="background:#1e40af;">
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:left;font-weight:600;">Judul Buku</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">Jatuh Tempo</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">Status</th>
                                    </tr>
                                    {$bookRows}
                                </table>
                                <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.7;">Jika ada denda keterlambatan, silakan selesaikan di loket perpustakaan.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
                                <p style="margin:0;color:#9ca3af;font-size:12px;">Email ini dikirim otomatis oleh sistem {$appName}.<br>Harap tidak membalas email ini.</p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>
        HTML;
    }

    private function buildStrukTemplate(object $loan, array $loanItems): string
    {
        $appName    =  $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan Mitra';
        $loanDate   = date('d F Y H:i', strtotime($loan->CreateDate));
        $firstDue   = !empty($loanItems) ? date('d F Y', strtotime($loanItems[0]->DueDate)) : '-';
        $totalBooks = count($loanItems);

        $bookRows = '';
        foreach ($loanItems as $i => $item) {
            $rowBg   = ($i % 2 === 0) ? '#f8fafc' : '#ffffff';
            $dueDate = date('d F Y', strtotime($item->DueDate));
            $bookRows .= <<<ROW
            <tr style="background:{$rowBg};">
                <td style="padding:10px 16px;color:#374151;font-size:13px;border-bottom:1px solid #f1f5f9;">
                    <strong>{$item->Title}</strong><br>
                    <span style="color:#9ca3af;font-size:11px;">{$item->NomorBarcode}</span>
                </td>
                <td style="padding:10px 16px;color:#6b7280;font-size:12px;text-align:center;border-bottom:1px solid #f1f5f9;">{$item->CallNumber}</td>
                <td style="padding:10px 16px;color:#1e40af;font-size:12px;font-weight:600;text-align:center;border-bottom:1px solid #f1f5f9;white-space:nowrap;">{$dueDate}</td>
            </tr>
            ROW;
        }

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f4f6f9;font-family:'Segoe UI',Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 0;">
                <tr><td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                        <tr>
                            <td style="background:linear-gradient(135deg,#0f2356,#1B3878);padding:32px 40px;text-align:center;">
                                <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;">📚 {$appName}</h1>
                                <p style="margin:8px 0 0;color:#bfdbfe;font-size:13px;">Struk Peminjaman Buku</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f0fdf4;border-left:4px solid #22c55e;padding:16px 40px;">
                                <p style="margin:0;color:#15803d;font-size:14px;font-weight:600;">✅ Peminjaman berhasil diproses.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:32px 40px;">
                                <p style="margin:0 0 8px;color:#374151;font-size:15px;">Kepada Yth. <strong>{$loan->Fullname}</strong> ({$loan->MemberNo}),</p>
                                <p style="margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;">Berikut struk peminjaman Anda.</p>
                                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr><td style="background:#1e40af;padding:12px 20px;"><p style="margin:0;color:#ffffff;font-size:13px;font-weight:600;">📋 Informasi Peminjaman</p></td></tr>
                                    <tr><td style="padding:20px;">
                                        <table width="100%" cellpadding="4" cellspacing="0">
                                            <tr><td style="color:#6b7280;font-size:13px;width:140px;">ID Peminjaman</td><td style="color:#374151;font-size:13px;font-weight:600;">: #{$loan->ID}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Tanggal Pinjam</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$loanDate}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Nomor Anggota</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$loan->MemberNo}</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Jumlah Buku</td><td style="color:#374151;font-size:13px;font-weight:600;">: {$totalBooks} buku</td></tr>
                                            <tr><td style="color:#6b7280;font-size:13px;">Tanggal Kembali</td><td style="color:#ef4444;font-size:13px;font-weight:700;">: {$firstDue}</td></tr>
                                        </table>
                                    </td></tr>
                                </table>
                                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                                    <tr style="background:#1e40af;">
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:left;font-weight:600;">Judul Buku</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">No. Panggil</th>
                                        <th style="padding:10px 16px;color:#fff;font-size:12px;text-align:center;font-weight:600;">Tgl Kembali</th>
                                    </tr>
                                    {$bookRows}
                                </table>
                                <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.7;">Harap kembalikan buku sebelum tanggal jatuh tempo untuk menghindari denda.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
                                <p style="margin:0;color:#9ca3af;font-size:12px;">Email ini dikirim otomatis oleh sistem {$appName}.<br>Harap tidak membalas email ini.</p>
                            </td>
                        </tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>
        HTML;
    }
}