<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnggotaExportMenu extends Migration
{
    public function up()
    {
        $menus = $this->db->table('c_menus');
        if (!$menus->where('controller', 'anggota/export_view')->countAllResults()) {
            $menus->insert([
                'name' => 'Export Anggota',
                'parent' => 23,
                'controller' => 'anggota/export_view',
                'slug' => 'export-anggota',
                'icon' => 'fas fa-file-export',
                'permission' => 'access|index|export',
                'type' => 'menu',
                'category_id' => 1,
                'category' => 'backend-menu',
                'sort' => 17,
                'active' => 1,
            ]);
        }

        $permissions = $this->db->table('auth_permissions');
        foreach (['anggota/export_view/access', 'anggota/export_view/index', 'anggota/export'] as $name) {
            $permission = $permissions->where('name', $name)->get()->getRow();
            if (!$permission) {
                $permissions->insert([
                    'name' => $name,
                    'route' => $name === 'anggota/export' ? $name : 'anggota/export_view',
                    'menu' => 'Export Anggota',
                    'description' => 'Export data anggota',
                ]);
                $permissionId = $this->db->insertID();
            } else {
                $permissionId = $permission->id;
            }

            $importAccess = $permissions->where('name', 'anggota/import_view/access')->get()->getRow();
            if (!$importAccess) {
                continue;
            }
            $grants = $this->db->table('auth_groups_permissions')
                ->where('permission_id', $importAccess->id)->get()->getResultArray();
            foreach ($grants as $grant) {
                $groupPermission = $this->db->table('auth_groups_permissions');
                if (!$groupPermission->where('group_id', $grant['group_id'])
                    ->where('permission_id', $permissionId)
                    ->where('branch_id', $grant['branch_id'])
                    ->countAllResults()) {
                    $groupPermission->insert([
                        'group_id' => $grant['group_id'],
                        'permission_id' => $permissionId,
                        'category' => $grant['category'],
                        'branch_id' => $grant['branch_id'],
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $permissionNames = ['anggota/export_view/access', 'anggota/export_view/index', 'anggota/export'];
        $permissionIds = array_column($this->db->table('auth_permissions')->select('id')
            ->whereIn('name', $permissionNames)->get()->getResultArray(), 'id');
        if ($permissionIds) {
            $this->db->table('auth_groups_permissions')->whereIn('permission_id', $permissionIds)->delete();
            $this->db->table('auth_permissions')->whereIn('id', $permissionIds)->delete();
        }
        $this->db->table('c_menus')->where('controller', 'anggota/export_view')->delete();
    }
}