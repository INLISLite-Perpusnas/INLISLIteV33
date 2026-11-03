<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class PengaturanFrontendAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }

        $parent = $this->db->table('c_menus')
            ->where('controller', 'pengaturan-umum')->where('category_id', 1)->get()->getRow();
        if (!$parent) {
            throw new RuntimeException('Menu Pengaturan Umum tidak ditemukan.');
        }

        $this->db->transBegin();

        $data = [
            'name' => 'Cache & Halaman Frontend',
            'parent' => $parent->id,
            'controller' => 'master-pengaturan-frontend',
            'slug' => 'pengaturan-frontend',
            'icon' => 'fas fa-sliders-h',
            'permission' => 'access|index|create|edit|delete',
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'sort' => 47,
            'active' => 1,
        ];
        $menu = $this->db->table('c_menus')
            ->where('controller', $data['controller'])->where('category_id', 1)->get()->getRow();
        if ($menu) {
            $this->db->table('c_menus')->where('id', $menu->id)->update($data);
        } else {
            $this->db->table('c_menus')->insert($data);
        }

        foreach (['access', 'index', 'create', 'edit', 'delete'] as $action) {
            $name = 'master-pengaturan-frontend/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'master-pengaturan-frontend',
                    'menu' => 'Cache & Halaman Frontend',
                    'description' => 'Permission ' . $action . ' menu Cache & Halaman Frontend',
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
                    'group_id' => $admin->id, 'permission_id' => $permissionId,
                ]);
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Cache & Halaman Frontend.');
        }
        $this->db->transCommit();
    }
}
