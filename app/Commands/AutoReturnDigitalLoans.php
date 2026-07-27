<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class AutoReturnDigitalLoans extends BaseCommand
{
    protected $group       = 'Loans';
    protected $name        = 'loans:auto-return-digital';
    protected $description = 'Scan pinjaman digital yang sudah expired dan auto-return.';

    public function run(array $params)
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        // Ambil semua item pinjaman digital yang masih 'Loan' dan sudah lewat DueDate
        $loanItems = $db->table('collectionloanitems cli')
            ->select('cli.*, c.ID as collection_id, c.ISDRM')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->where('cli.LoanStatus', 'Loan')
            ->where('c.ISDRM', 1)
            ->where('cli.DueDate <=', $now)
            ->get()
            ->getResult();

        CLI::write('Ditemukan ' . count($loanItems) . ' item untuk auto-return.', 'yellow');

        $success = 0;
        $failed  = 0;

        foreach ($loanItems as $loanItem) {
            try {
                $this->autoReturn($db, $loanItem, $loanItem->collection_id);
                $success++;
                CLI::write("OK  - LoanItem ID {$loanItem->ID}", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::error("GAGAL - LoanItem ID {$loanItem->ID}: " . $e->getMessage());
                log_message('error', 'AutoReturnDigitalLoans: ' . $e->getMessage());
            }
        }

        CLI::write("Selesai. Berhasil: {$success}, Gagal: {$failed}", 'cyan');
    }

    private function autoReturn($db, $loanItem, int $collection_id)
    {
        $now = date('Y-m-d H:i:s');
        $ip  = '127.0.0.1'; // CLI tidak punya IP request, pakai placeholder/system

        $db->transBegin();

        $db->table('collectionloanitems')
            ->where('ID', $loanItem->ID)
            ->update([
                'LoanStatus'     => 'Return',
                'ActualReturn'   => $now,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        $loan = $db->table('collectionloans')
            ->where('ID', $loanItem->CollectionLoan_id)
            ->get()
            ->getRow();

        if ($loan) {
            $db->table('collectionloans')
                ->where('ID', $loanItem->CollectionLoan_id)
                ->update([
                    'ReturnCount'    => (int) ($loan->ReturnCount ?? 0) + 1,
                    'UpdateDate'     => $now,
                    'UpdateTerminal' => $ip,
                ]);
        }

        $db->table('collections')
            ->where('ID', $collection_id)
            ->update([
                'Status_id'      => 1,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        if ($db->transStatus() === false) {
            $db->transRollback();
            throw new \RuntimeException('Transaksi gagal untuk LoanItem ID ' . $loanItem->ID);
        }

        $db->transCommit();
    }
}