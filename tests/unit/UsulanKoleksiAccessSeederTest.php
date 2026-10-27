<?php

use App\Database\Seeds\UsulanKoleksiAccessSeeder;
use PHPUnit\Framework\TestCase;

final class UsulanKoleksiAccessSeederTest extends TestCase
{
    public function testFindsMenuByControllerOrNameAndCanBeRerun(): void
    {
        foreach (['menu-koleksi', ''] as $controller) {
            $db = $this->database();
            $db->table('auth_groups')->insert(['id' => 1, 'name' => 'admin']);
            $db->table('c_menus')->insert([
                'id' => 254, 'name' => 'Koleksi', 'controller' => $controller,
                'parent' => 0, 'category_id' => 1,
            ]);

            $seeder = new UsulanKoleksiAccessSeeder(config(\Config\Database::class), $db);
            $seeder->run();
            $seeder->run();

            $menu = $db->table('c_menus')->where('controller', 'usulankoleksi')->get()->getRow();
            $this->assertSame(254, (int) $menu->parent);
            $this->assertSame(1, $db->table('c_menus')->where('controller', 'usulankoleksi')->countAllResults());
            $this->assertSame(2, $db->table('auth_permissions')->countAllResults());
            $this->assertSame(2, $db->table('auth_groups_permissions')->countAllResults());
            $db->close();
        }
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
