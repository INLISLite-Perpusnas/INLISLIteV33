<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

/**
 * Menu "Koleksi Dipesan" di bawah Sirkulasi + permission-nya untuk grup admin dan sirkulasi.
 * Aman dijalankan berulang.
 */
class PemesananKoleksiAccessSeeder extends Seeder
{
    public function run()
    {
        $parent = $this->db->table('c_menus')
            ->where('controller', 'menu-sirkulasi')->where('parent', 0)->where('category_id', 1)->get()->getRow();
        if (!$parent) {
            throw new RuntimeException('Menu induk Sirkulasi tidak ditemukan.');
        }

        $this->db->transBegin();

        $menu = [
            'name' => 'Koleksi Dipesan',
            'parent' => $parent->id,
            'controller' => 'sirkulasi-pemesanan',
            'slug' => 'sirkulasi-pemesanan',
            'icon' => 'fas fa-bookmark',
            'permission' => 'access|index|delete',
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'sort' => 28,
            'active' => 1,
        ];
        $existing = $this->db->table('c_menus')->where('controller', 'sirkulasi-pemesanan')->get()->getRow();
        if ($existing) {
            $this->db->table('c_menus')->where('id', $existing->id)->update($menu);
        } else {
            $this->db->table('c_menus')->insert($menu);
        }

        $groups = $this->db->table('auth_groups')->whereIn('name', ['admin', 'sirkulasi'])->get()->getResult();
        foreach (['access', 'index', 'delete'] as $action) {
            $name = 'sirkulasi-pemesanan/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if ($permission) {
                $permissionId = $permission->id;
            } else {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'sirkulasi-pemesanan',
                    'menu' => 'Koleksi Dipesan',
                    'description' => 'Update Permission for menu Koleksi Dipesan',
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
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Koleksi Dipesan.');
        }
        $this->db->transCommit();
    }
}
