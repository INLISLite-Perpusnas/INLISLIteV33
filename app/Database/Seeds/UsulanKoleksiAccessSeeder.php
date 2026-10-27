<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class UsulanKoleksiAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        $parent = $this->db->table('c_menus')
            ->where('controller', 'menu-koleksi')
            ->where('category_id', 1)
            ->where('parent', 0)
            ->get()
            ->getRow();
        if (!$parent) {
            $parent = $this->db->table('c_menus')
                ->where('name', 'Koleksi')
                ->where('category_id', 1)
                ->where('parent', 0)
                ->get()
                ->getRow();
        }

        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }
        if (!$parent) {
            throw new RuntimeException('Menu induk Koleksi tidak ditemukan pada kategori backend.');
        }

        $this->db->transStart();

        $menu = $this->db->table('c_menus')
            ->where('controller', 'usulankoleksi')
            ->where('category_id', 1)
            ->get()
            ->getRow();
        if (!$menu) {
            $this->db->table('c_menus')->insert([
                'name' => 'Usulan Koleksi',
                'parent' => $parent->id,
                'controller' => 'usulankoleksi',
                'slug' => 'usulan-koleksi',
                'icon' => 'pe-7s-notebook',
                'permission' => 'access|index',
                'type' => 'menu',
                'category_id' => 1,
                'category' => 'backend-menu',
                'sort' => 20,
                'active' => 1,
            ]);
        }

        foreach (['access', 'index'] as $action) {
            $name = 'usulankoleksi/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'usulankoleksi',
                    'menu' => 'Usulan Koleksi',
                    'description' => 'Akses daftar Usulan Koleksi',
                ]);
                $permissionId = $this->db->insertID();
            } else {
                $permissionId = $permission->id;
            }

            $assigned = $this->db->table('auth_groups_permissions')
                ->where('group_id', $admin->id)
                ->where('permission_id', $permissionId)
                ->countAllResults();
            if (!$assigned) {
                $this->db->table('auth_groups_permissions')->insert([
                    'group_id' => $admin->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Usulan Koleksi.');
        }
    }
}
