<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsDownload extends Migration
{
    protected $DBGroup = 'data';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'User_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'ip' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],
            'catalogfilesID' => [
                'type' => 'INT',
                'null' => true,
            ],
            'isLKD' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'default' => 0,
            ],
            'waktu' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'Branch_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'active' => [
                'type' => 'INT',
                'null' => true,
                'default' => 1,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('User_id', false, false, 'fk_logsDownload_1_idx');
        $this->forge->addKey('catalogfilesID', false, false, 'fk_logsDownload_catalogfiles_idx');
        $this->forge->createTable('logsdownload', true, [
            'ENGINE' => 'InnoDB',
            'CHARACTER SET' => 'latin1',
            'COLLATE' => 'latin1_swedish_ci',
        ]);
    }

    public function down()
    {
        return;
    }
}
