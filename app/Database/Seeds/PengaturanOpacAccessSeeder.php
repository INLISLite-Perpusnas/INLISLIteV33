<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class PengaturanOpacAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }

        $this->db->transBegin();

        $parentId = $this->upsertMenu([
            'name' => 'Pengaturan Opac',
            'parent' => 0,
            'controller' => 'pengaturan-opac',
            'slug' => 'pengaturan-opac',
            'icon' => 'fas fa-globe',
            'permission' => 'access',
            'sort' => 83,
        ]);
        $this->grantPermissions($admin->id, 'Pengaturan Opac', ['pengaturan-opac/access' => 'Akses menu Pengaturan Opac']);

        $children = [
            ['Koleksi Unggulan', 'master-opac-koleksi-unggulan', 'fas fa-star'],
            ['Koleksi Terbaru', 'master-opac-koleksi-terbaru', 'fas fa-book-open'],
            ['Koleksi Sering Dipinjam', 'master-opac-koleksi-sering-dipinjam', 'fas fa-fire'],
            ['Koleksi Sering Baca di Tempat', 'master-opac-baca-ditempat', 'fas fa-book-reader'],
            ['Pemesanan Koleksi', 'master-opac-booking', 'fas fa-shopping-basket'],
            ['Usulan Koleksi', 'master-opac-usulan-koleksi', 'fas fa-lightbulb'],
            ['Pengaturan Faset', 'master-opac-faset', 'fas fa-filter'],
            ['Log Pencarian OPAC', 'master-opac-logs', 'fas fa-clipboard-list'],
            ['Histori Pencarian', 'master-opac-histori-pencarian', 'fas fa-history'],
        ];

        foreach ($children as $sort => [$name, $controller, $icon]) {
            $this->upsertMenu([
                'name' => $name,
                'parent' => $parentId,
                'controller' => $controller,
                'slug' => str_replace('master-', '', $controller),
                'icon' => $icon,
                'permission' => 'access|index|create|edit|delete',
                'sort' => $sort + 1,
            ]);

            $actions = [];
            foreach (['access', 'index', 'create', 'edit', 'delete'] as $action) {
                $actions[$controller . '/' . $action] = 'Permission ' . $action . ' menu ' . $name;
            }
            $this->grantPermissions($admin->id, $name, $actions);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Pengaturan Opac.');
        }
        $this->db->transCommit();
        reloadPermission();
    }

    private function upsertMenu(array $data): int
    {
        $menu = $this->db->table('c_menus')
            ->where('controller', $data['controller'])->where('category_id', 1)->get()->getRow();

        $data += [
            'type' => 'menu',
            'category_id' => 1,
            'category' => 'backend-menu',
            'active' => 1,
        ];

        if ($menu) {
            $this->db->table('c_menus')->where('id', $menu->id)->update($data);
            return (int) $menu->id;
        }

        $this->db->table('c_menus')->insert($data);
        return (int) $this->db->insertID();
    }

    private function grantPermissions(int $groupId, string $menuName, array $actions): void
    {
        foreach ($actions as $name => $description) {
            $permission = $this->db->table('auth_permissions')->where('name', $name)->get()->getRow();
            if (!$permission) {
                $this->db->table('auth_permissions')->insert([
                    'name' => $name,
                    'route' => explode('/', $name, 2)[0],
                    'menu' => $menuName,
                    'description' => $description,
                ]);
                $permissionId = $this->db->insertID();
            } else {
                $permissionId = $permission->id;
            }

            $assigned = $this->db->table('auth_groups_permissions')
                ->where('group_id', $groupId)
                ->where('permission_id', $permissionId)
                ->countAllResults();
            if (!$assigned) {
                $this->db->table('auth_groups_permissions')->insert([
                    'group_id' => $groupId, 'permission_id' => $permissionId,
                ]);
            }
        }
    }
}
