<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FrontendSettingParametersSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('data');

        // Penanda koleksi populer di katalog; hanya ditambahkan bila kolom belum ada.
        // Nama kolom MySQL tidak case-sensitive (sebagian database sudah punya 'ISPopuler'),
        // jadi cek tanpa membedakan huruf besar/kecil.
        $catalogFields = array_map('strtolower', $db->getFieldNames('catalogs'));
        if (!in_array('ispopuler', $catalogFields, true)) {
            $db->query('ALTER TABLE catalogs ADD COLUMN IsPopuler INT(2) NOT NULL DEFAULT 0');
        }

        $settings = [
            'is_opac_cache' => '1',
            'is_dashboard_cache' => '1',
            'is_home_cache' => '0',
            'isdrm' => '1',
            'is_keanggotaan_online' => '1',
            'is_perpustakaan_keliling' => '1',
            'is_populer' => '1',
            'is_sering_dipinjam' => '1',
            'is_sering_dibaca' => '0',
        ];

        foreach ($settings as $name => $value) {
            $existing = $db->table('settingparameters')->where('Name', $name)->get()->getRow();
            if ($name === 'is_keanggotaan_online' && !$existing) {
                $existing = $db->table('settingparameters')->where('Name', 'Is_keanggotaan_online')->get()->getRow();
            }
            if ($existing && $name === 'is_keanggotaan_online' && $existing->Name !== $name) {
                $db->table('settingparameters')->where('ID', $existing->ID)->update(['Name' => $name]);
            } elseif (!$existing) {
                $db->table('settingparameters')->insert([
                    'Name' => $name,
                    'Value' => $value,
                ]);
            }
        }
    }
}
