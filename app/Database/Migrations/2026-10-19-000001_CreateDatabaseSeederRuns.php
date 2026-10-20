<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDatabaseSeederRuns extends Migration
{
    protected $DBGroup = 'data';

    public function up()
    {
        $this->forge->addField([
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'checksum' => ['type' => 'CHAR', 'constraint' => 64],
            'executed_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('name');
        $this->forge->createTable('database_seeder_runs', true);
    }

    public function down()
    {
        $this->forge->dropTable('database_seeder_runs', true);
    }
}
