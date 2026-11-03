<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

/**
 * Menu "Nama Sumber Perolehan" di Administrasi > Pengaturan Akuisisi + permission-nya
 * untuk grup yang sudah punya akses Sumber Koleksi. Aman dijalankan berulang.
 */
class NamaSumberPerolehanAccessSeeder extends Seeder
{
    public function run()
    {
        // Induk menu = induk dari "Sumber Koleksi" (Pengaturan Akuisisi)
        $sibling = $this->db->table('c_menus')->where('controller', 'master-sumber-koleksi')->get()->getRow();
        if (!$sibling) {
            throw new RuntimeException('Menu Sumber Koleksi tidak ditemukan.');
        }

        $this->db->transBegin();

        $menu = [
            'name' => 'Nama Sumber Perolehan',
            'parent' => $sibling->parent,
            'controller' => 'master-nama-sumber-perolehan',
            'slug' => 'nama-sumber-perolehan',
            'icon' => 'fas fa-handshake',
            'permission' => 'access|index|create|edit|delete',
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'sort' => (int) $sibling->sort + 1,
            'active' => 1,
        ];
        $existing = $this->db->table('c_menus')->where('controller', $menu['controller'])->get()->getRow();
        if ($existing) {
            $this->db->table('c_menus')->where('id', $existing->id)->update($menu);
        } else {
            $this->db->table('c_menus')->insert($menu);
        }

        $groupIds = array_column($this->db->table('auth_groups_permissions gp')
            ->select('gp.group_id')
            ->join('auth_permissions p', 'p.id = gp.permission_id')
            ->where('p.name', 'master-sumber-koleksi/index')
            ->get()->getResultArray(), 'group_id');
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if ($admin) {
            $groupIds[] = $admin->id;
        }
        $groupIds = array_unique($groupIds);

        foreach (['access', 'index', 'create', 'edit', 'delete'] as $action) {
            $name = 'master-nama-sumber-perolehan/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if ($permission) {
                $permissionId = $permission->id;
            } else {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'master-nama-sumber-perolehan',
                    'menu' => 'Nama Sumber Perolehan',
                    'description' => 'Update Permission for menu Nama Sumber Perolehan',
                ]);
                $permissionId = $this->db->insertID();
            }

            foreach ($groupIds as $groupId) {
                $assigned = $this->db->table('auth_groups_permissions')
                    ->where('group_id', $groupId)->where('permission_id', $permissionId)->countAllResults();
                if (!$assigned) {
                    $this->db->table('auth_groups_permissions')->insert([
                        'group_id' => $groupId, 'permission_id' => $permissionId,
                    ]);
                }
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Nama Sumber Perolehan.');
        }
        $this->db->transCommit();
    }
}
