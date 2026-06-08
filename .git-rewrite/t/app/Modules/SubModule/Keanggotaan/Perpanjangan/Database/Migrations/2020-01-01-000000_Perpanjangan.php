<?php

namespace Perpanjangan\Database\Migrations;

use CodeIgniter\Database\Migration;

class Perpanjangan extends Migration
{
	public function up()
	{
		// t_perpanjangan
		$this->forge->dropTable('t_perpanjangan', true);
		$this->forge->addField([
			'id' 			=> ['type' => 'MEDIUMINT','constraint' => '11','unsigned' => true,'auto_increment' => true,],
			't_anggota_id' 	=> ['type' => 'INT','constraint' =>11,'null' => true,],
			'biaya' 			=> ['type' => 'DECIMAL' ,'null' => true,],
			'description' 	=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'is_lunas' 		=> ['type' => 'TINYINT','constraint' => '1','unsigned' => true,'default' => 1,],
			'created_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'updated_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'created_at' 	=> ['type' => 'DATETIME','null' => true,],
			'updated_at' 	=> ['type' => 'DATETIME','null' => true,],
			'deleted_at' 	=> ['type' => 'DATETIME','null' => true,],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_perpanjangan');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_perpanjangan', true);
	}
}
