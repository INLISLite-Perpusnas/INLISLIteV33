<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

/**
 * Menu & permission backoffice Perpustakaan Keliling (cms/perpustakaan-keliling/*) untuk grup admin.
 * Aman dijalankan ulang (upsert menu, permission & relasi grup hanya ditambah bila belum ada).
 *
 * php spark db:seed PerpustakaanKelilingCmsAccessSeeder
 */
class PerpustakaanKelilingCmsAccessSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->db->table('auth_groups')->where('name', 'admin')->get()->getRow();
        if (!$admin) {
            throw new RuntimeException('Grup admin tidak ditemukan pada database data.');
        }

        $this->db->transBegin();

        $parentId = $this->upsertMenu([
            'name' => 'Perpustakaan Keliling',
            'parent' => 0,
            'controller' => 'cms/perpustakaan-keliling',
            'slug' => 'perpustakaan-keliling-cms',
            'icon' => 'fas fa-bus',
            'permission' => 'access',
            'sort' => 6,
        ]);
        $this->grantPermissions($admin->id, 'Perpustakaan Keliling', [
            'cms/perpustakaan-keliling/access' => 'Akses menu Perpustakaan Keliling',
        ]);

        // Permission dicek per prefix URL: 'cms/perpustakaan-keliling/jadwal' juga mencakup
        // jadwal/detail/{id}, jadwal/export, dst. (lihat is_allowed() di app/Common.php)
        $children = [
            ['Jadwal Layanan', 'cms/perpustakaan-keliling/jadwal', 'fas fa-calendar-alt'],
            ['Unit Kendaraan', 'cms/perpustakaan-keliling/unit', 'fas fa-bus'],
            ['Lokasi Layanan', 'cms/perpustakaan-keliling/lokasi', 'fas fa-map-marker-alt'],
            ['Petugas', 'cms/perpustakaan-keliling/petugas', 'fas fa-user-friends'],
        ];

        foreach ($children as $sort => [$name, $controller, $icon]) {
            $this->upsertMenu([
                'name' => $name,
                'parent' => $parentId,
                'controller' => $controller,
                'slug' => str_replace(['cms/', '/'], ['', '-'], $controller),
                'icon' => $icon,
                'permission' => 'access|index|create|edit|delete',
                'sort' => $sort + 1,
            ]);

            $actions = [$controller => 'Permission menu ' . $name];
            foreach (['access', 'index', 'create', 'edit', 'delete'] as $action) {
                $actions[$controller . '/' . $action] = 'Permission ' . $action . ' menu ' . $name;
            }
            $this->grantPermissions($admin->id, $name, $actions);
        }

        // Dokumentasi kegiatan dikelola dari detail jadwal
        $this->grantPermissions($admin->id, 'Jadwal Layanan', [
            'cms/perpustakaan-keliling/dokumentasi' => 'Permission dokumentasi kegiatan Perpustakaan Keliling',
        ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new RuntimeException('Gagal mendaftarkan menu dan permission Perpustakaan Keliling.');
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
                    'route' => 'cms/perpustakaan-keliling',
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
