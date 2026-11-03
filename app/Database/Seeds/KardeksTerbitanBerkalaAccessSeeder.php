<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

/**
 * Menu "Kardeks Terbitan Berkala" di bawah Koleksi + permission-nya untuk grup admin dan kataloger.
 * Aman dijalankan berulang.
 */
class KardeksTerbitanBerkalaAccessSeeder extends Seeder
{
    public function run()
    {
        // Induk menu = induk dari "Daftar Eksemplar" yang aktif (menu Koleksi)
        $sibling = $this->db->table('c_menus')
            ->where('controller', 'eksemplar')->where('active', 1)->where('category_id', 1)
            ->orderBy('id', 'DESC')->get()->getRow();
        if (!$sibling) {
            throw new RuntimeException('Menu Daftar Eksemplar tidak ditemukan.');
        }

        $this->db->transBegin();

        $menu = [
            'name' => 'Kardeks Terbitan Berkala',
            'parent' => $sibling->parent,
            'controller' => 'kardeks-terbitan-berkala',
            'slug' => 'kardeks-terbitan-berkala',
            'icon' => 'fas fa-newspaper',
            'permission' => 'access|index',
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'sort' => 12,
            'active' => 1,
        ];
        $existing = $this->db->table('c_menus')->where('controller', 'kardeks-terbitan-berkala')->get()->getRow();
        if ($existing) {
            $this->db->table('c_menus')->where('id', $existing->id)->update($menu);
        } else {
            $this->db->table('c_menus')->insert($menu);
        }

        $groups = $this->db->table('auth_groups')->whereIn('name', ['admin', 'kataloger'])->get()->getResult();
        foreach (['access', 'index'] as $action) {
            $name = 'kardeks-terbitan-berkala/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if ($permission) {
                $permissionId = $permission->id;
            } else {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'kardeks-terbitan-berkala',
                    'menu' => 'Kardeks Terbitan Berkala',
                    'description' => 'Update Permission for menu Kardeks Terbitan Berkala',
                ]);
                $permissionId = $this->db->insertID();
            }

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

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Kardeks Terbitan Berkala.');
        }
        $this->db->transCommit();
    }
}
