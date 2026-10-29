<?php

use App\Database\Seeds\SurveyAdminAccessSeeder;
use PHPUnit\Framework\TestCase;

final class SurveyAdminAccessSeederTest extends TestCase
{
    public function testMissingAdminGroupDoesNotCreateMenuOrPermissions(): void
    {
        $db = $this->database();

        try {
            (new SurveyAdminAccessSeeder(config(\Config\Database::class), $db))->run();
            $this->fail('Seeder harus menolak database tanpa grup admin.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Grup admin tidak ditemukan', $exception->getMessage());
        }

        $this->assertSame(0, $db->table('c_menus')->countAllResults());
        $this->assertSame(0, $db->table('auth_permissions')->countAllResults());
        $db->close();
    }

    public function testRegistersSidebarMenuAndAdminPermissionsOnlyOnce(): void
    {
        $db = $this->database();
        $db->table('auth_groups')->insertBatch([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'petugas'],
        ]);
        $seeder = new SurveyAdminAccessSeeder(config(\Config\Database::class), $db);

        $seeder->run();
        $seeder->run();

        $menu = $db->table('c_menus')->where('controller', 'surveipemustaka')->get()->getRow();
        $this->assertSame(0, (int) $menu->parent);
        $this->assertSame(1, (int) $menu->category_id);
        $this->assertSame(1, (int) $menu->active);
        $this->assertSame(1, $db->table('c_menus')->countAllResults());
        $this->assertSame(7, $db->table('auth_permissions')->countAllResults());
        $this->assertSame(7, $db->table('auth_groups_permissions')->where('group_id', 1)->countAllResults());
        $this->assertSame(0, $db->table('auth_groups_permissions')->where('group_id', 2)->countAllResults());
        $this->assertSame(1, $db->table('auth_permissions')
            ->where('name', 'surveipemustaka/access')->countAllResults());
        $this->assertSame(1, $db->table('auth_permissions')
            ->where('name', 'survei/create')->countAllResults());
        $db->close();
    }

    public function testReusesExistingMenuAndPermissions(): void
    {
        $db = $this->database();
        $db->table('auth_groups')->insert(['id' => 1, 'name' => 'admin']);
        $db->table('c_menus')->insert([
            'id' => 22, 'name' => 'Survei Pemustaka', 'controller' => 'survei',
            'parent' => 0, 'category_id' => 1, 'active' => 0,
        ]);
        $db->table('auth_permissions')->insert([
            'id' => 50, 'name' => 'survei/create', 'route' => 'survei',
        ]);

        (new SurveyAdminAccessSeeder(config(\Config\Database::class), $db))->run();

        $menu = $db->table('c_menus')->where('id', 22)->get()->getRow();
        $this->assertSame('surveipemustaka', $menu->controller);
        $this->assertSame(1, (int) $menu->active);
        $this->assertSame(1, $db->table('c_menus')->countAllResults());
        $this->assertSame(7, $db->table('auth_permissions')->countAllResults());
        $this->assertSame(1, $db->table('auth_groups_permissions')
            ->where('group_id', 1)->where('permission_id', 50)->countAllResults());
        $db->close();
    }

    private function database()
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:',
            'DBPrefix' => '', 'pConnect' => false, 'DBDebug' => true,
        ], false);
        $db->query('CREATE TABLE auth_groups (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query('CREATE TABLE c_menus (id INTEGER PRIMARY KEY, name TEXT, controller TEXT, parent INTEGER, category_id INTEGER, slug TEXT, icon TEXT, permission TEXT, type TEXT, category TEXT, sort INTEGER, active INTEGER)');
        $db->query('CREATE TABLE auth_permissions (id INTEGER PRIMARY KEY, name TEXT, route TEXT, menu TEXT, description TEXT)');
        $db->query('CREATE TABLE auth_groups_permissions (group_id INTEGER, permission_id INTEGER)');
        return $db;
    }
}
