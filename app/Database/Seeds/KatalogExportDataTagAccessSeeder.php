<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Permission "katalog/export_data_tag" (Export Data Tag di Daftar & Karantina Katalog)
 * untuk grup admin dan kataloger. Aman dijalankan berulang.
 */
class KatalogExportDataTagAccessSeeder extends Seeder
{
    public function run()
    {
        $name = 'katalog/export_data_tag';
        $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
        if ($permission) {
            $permissionId = $permission->id;
        } else {
            $this->db->table('auth_permissions')->insert([
                'name' => $name,
                'route' => $name,
                'menu' => 'Koleksi',
                'description' => 'Export data tag katalog ke Excel',
            ]);
            $permissionId = $this->db->insertID();
        }

        $groups = $this->db->table('auth_groups')->whereIn('name', ['admin', 'kataloger'])->get()->getResult();
        foreach ($groups as $group) {
            $assigned = $this->db->table('auth_groups_permissions')
                ->where('group_id', $group->id)->where('permission_id', $permissionId)->countAllResults();
            if (!$assigned) {
                $this->db->table('auth_groups_permissions')->insert([
                    'group_id' => $group->id, 'permission_id' => $permissionId,
                ]);
            }
        }
    }
}
