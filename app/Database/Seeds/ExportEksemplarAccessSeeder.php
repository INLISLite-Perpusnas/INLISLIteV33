<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class ExportEksemplarAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }

        // Ditempatkan satu induk dengan menu Import Excel (eksemplar/importviews)
        $sibling = $this->db->table('c_menus')
            ->where('controller', 'eksemplar/importviews')->where('category_id', 1)->get()->getRow();
        if (!$sibling) {
            throw new RuntimeException('Menu Import Excel eksemplar tidak ditemukan.');
        }

        $this->db->transBegin();

        $data = [
            'name' => 'Export Excel',
            'parent' => $sibling->parent,
            'controller' => 'export-eksemplar',
            'slug' => 'export-eksemplar',
            'icon' => 'fas fa-file-export',
            'permission' => 'access|index|export|download',
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'sort' => $sibling->sort,
            'active' => 1,
        ];
        $menu = $this->db->table('c_menus')
            ->where('controller', $data['controller'])->where('category_id', 1)->get()->getRow();
        if ($menu) {
            $this->db->table('c_menus')->where('id', $menu->id)->update($data);
        } else {
            $this->db->table('c_menus')->insert($data);
        }

        foreach (['access', 'index', 'export', 'download'] as $action) {
            $name = 'export-eksemplar/' . $action;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => 'export-eksemplar',
                    'menu' => 'Export Excel',
                    'description' => 'Permission ' . $action . ' menu Export Excel Eksemplar',
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
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Export Excel Eksemplar.');
        }
        $this->db->transCommit();
    }
}
