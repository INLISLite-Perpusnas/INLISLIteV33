<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOpacSearchHistoryReport extends Migration
{
    public function up()
    {
        $parent = $this->db->table('c_menus')->select('id')->where('controller', 'menu-laporan')->get()->getRow();
        $menus = $this->db->table('c_menus');
        if ($parent && !$menus->where('controller', 'laporan-opac-histori-pencarian')->countAllResults()) {
            $menus->insert([
                'name' => 'Laporan Histori Pencarian OPAC',
                'parent' => $parent->id,
                'controller' => 'laporan-opac-histori-pencarian',
                'slug' => 'laporan-opac-histori-pencarian',
                'icon' => 'fas fa-search',
                'permission' => 'access|index|export|export_pdf',
                'type' => 'menu',
                'category_id' => 1,
                'category' => 'backend-menu',
                'sort' => 39,
                'active' => 1,
            ]);
        }

        $permissions = $this->db->table('auth_permissions');
        $historyAccess = $permissions->where('name', 'master-opac-histori-pencarian/access')->get()->getRow();
        $grants = $historyAccess
            ? $this->db->table('auth_groups_permissions')->where('permission_id', $historyAccess->id)->get()->getResultArray()
            : [];
        foreach (['access', 'index', 'export', 'export_pdf'] as $action) {
            $name = 'laporan-opac-histori-pencarian/' . $action;
            $permission = $permissions->where('name', $name)->get()->getRow();
            if (!$permission) {
                $permissions->insert([
                    'name' => $name,
                    'route' => $action === 'access' || $action === 'index' ? 'laporan-opac-histori-pencarian' : $name,
                    'menu' => 'Laporan Histori Pencarian OPAC',
                    'description' => 'Laporan histori pencarian OPAC',
                ]);
                $permissionId = $this->db->insertID();
            } else {
                $permissionId = $permission->id;
            }
            foreach ($grants as $grant) {
                $groupPermission = $this->db->table('auth_groups_permissions');
                if (!$groupPermission->where('group_id', $grant['group_id'])
                    ->where('permission_id', $permissionId)
                    ->where('branch_id', $grant['branch_id'])->countAllResults()) {
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
        $names = array_map(static fn ($action) => 'laporan-opac-histori-pencarian/' . $action,
            ['access', 'index', 'export', 'export_pdf']);
        $ids = array_column($this->db->table('auth_permissions')->select('id')
            ->whereIn('name', $names)->get()->getResultArray(), 'id');
        if ($ids) {
            $this->db->table('auth_groups_permissions')->whereIn('permission_id', $ids)->delete();
            $this->db->table('auth_permissions')->whereIn('id', $ids)->delete();
        }
        $this->db->table('c_menus')->where('controller', 'laporan-opac-histori-pencarian')->delete();
    }
}
