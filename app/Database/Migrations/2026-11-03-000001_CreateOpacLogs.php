<?php

namespace App\Database\Migrations;

use App\Database\Seeds\PengaturanOpacAccessSeeder;
use CodeIgniter\Database\Migration;

class CreateOpacLogs extends Migration
{
    protected $DBGroup = 'data';

    public function up()
    {
        $this->forge->addField([
            'ID' => ['type' => 'BIGINT', 'auto_increment' => true],
            'waktu' => ['type' => 'DATETIME'],
            'User_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ip' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'jenis_pencarian' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'keyword' => ['type' => 'TEXT', 'null' => true],
            'jenis_bahan' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'url' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('ID');
        $this->forge->addKey('waktu');
        $this->forge->createTable('opaclogs', true);

        if (!$this->db->table('settingparameters')->where('Name', 'opac_log')->countAllResults()) {
            $this->db->table('settingparameters')->insert(['Name' => 'opac_log', 'Value' => 'FALSE']);
        }

        if ($this->db->table('auth_groups')->where('name', 'admin')->countAllResults()) {
            (new PengaturanOpacAccessSeeder(config(\Config\Database::class), $this->db))->run();
        }
    }

    public function down()
    {
    }
}
