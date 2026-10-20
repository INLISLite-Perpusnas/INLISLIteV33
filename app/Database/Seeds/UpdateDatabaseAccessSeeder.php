<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class UpdateDatabaseAccessSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('data');
        $admin = $db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan.');
        }

        $name = 'master-nama-perpustakaan/update-database';
        $permission = $db->table('auth_permissions')->where('name', $name)->get()->getRow();
        if (!$permission) {
            $db->table('auth_permissions')->insert([
                'name' => $name,
                'route' => 'master-nama-perpustakaan',
                'menu' => 'Nama Perpustakaan',
                'description' => 'Admin dapat menjalankan update database',
            ]);
            $permissionId = $db->insertID();
        } else {
            $permissionId = $permission->id;
        }

        $assigned = $db->table('auth_groups_permissions')
            ->where('group_id', $admin->id)
            ->where('permission_id', $permissionId)
            ->countAllResults();
        if (!$assigned) {
            $db->table('auth_groups_permissions')->insert([
                'group_id' => $admin->id,
                'permission_id' => $permissionId,
            ]);
        }
    }
}
