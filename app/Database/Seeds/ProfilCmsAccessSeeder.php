<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class ProfilCmsAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan.');
        }

        $this->db->transStart();

        $menu = $this->db->table('c_menus')->where('controller', 'cms/profil')->get()->getRow();
        if (!$menu) {
            $this->db->table('c_menus')->insert([
                'name'        => 'Profil & Layanan',
                'parent'      => 0,
                'controller'  => 'cms/profil',
                'slug'        => 'profil-layanan',
                'icon'        => 'fas fa-address-card',
                'permission'  => 'access|index|edit',
                'type'        => 'menu',
                'category_id' => 1,
                'category'    => 'backend-menu',
                'sort'        => 5,
                'active'      => 1,
            ]);
        }

        $routes = [
            'access',
            'index',
            'edit',
            'layanan/datatable',
            'layanan/save',
            'layanan/update-order',
            'layanan/get',
            'layanan/delete',
        ];

        foreach ($routes as $route) {
            $name = 'cms/profil/' . $route;
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name'        => $name,
                    'route'       => 'cms/profil',
                    'menu'        => 'Profil & Layanan',
                    'description' => 'Permission untuk Profil & Layanan',
                ]);
                $permissionId = $this->db->insertID();
            } else {
                $permissionId = $permission->id;
            }

            $groupPermission = $this->db->table('auth_groups_permissions')
                ->where('group_id', $admin->id)
                ->where('permission_id', $permissionId)
                ->countAllResults();

            if (!$groupPermission) {
                $this->db->table('auth_groups_permissions')->insert([
                    'group_id'      => $admin->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Profil CMS.');
        }
    }
}
