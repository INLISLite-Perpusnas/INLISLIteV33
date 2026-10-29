<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class SurveyAdminAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }

        $this->db->transBegin();

        $menu = $this->db->table('c_menus')
            ->where('controller', 'surveipemustaka')->where('category_id', 1)->get()->getRow();
        if (!$menu) {
            $menu = $this->db->table('c_menus')
                ->where('name', 'Survei Pemustaka')->where('category_id', 1)->get()->getRow();
        }

        if (!$menu) {
            $this->db->table('c_menus')->insert([
                'name' => 'Survei Pemustaka',
                'parent' => 0,
                'controller' => 'surveipemustaka',
                'slug' => 'survei-pemustaka',
                'icon' => 'pe-7s-graph2',
                'permission' => 'access|index|manage',
                'type' => 'menu',
                'category_id' => 1,
                'category' => 'backend-menu',
                'sort' => 30,
                'active' => 1,
            ]);
        } else {
            $this->db->table('c_menus')->where('id', $menu->id)->update([
                'controller' => 'surveipemustaka', 'active' => 1,
            ]);
        }

        $actions = [
            'surveipemustaka/access' => 'Akses menu Survei Pemustaka',
            'surveipemustaka/index' => 'Akses halaman Survei Pemustaka',
            'surveipemustaka/manage' => 'Kelola Survei Pemustaka',
            'surveipemustaka/save-survey' => 'Simpan survei',
            'surveipemustaka/save-question' => 'Simpan pertanyaan survei',
            'surveipemustaka/save-option' => 'Simpan pilihan survei',
            'survei/create' => 'Izin mengelola survei pada controller lama',
        ];

        foreach ($actions as $name => $description) {
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => explode('/', $name, 2)[0],
                    'menu' => 'Survei Pemustaka',
                    'description' => $description,
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
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Survei Pemustaka.');
        }
        $this->db->transCommit();
    }
}
