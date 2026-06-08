<?php

namespace UsulanKoleksi\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsulanKoleksi extends Migration
{
	public function up()
	{
		// t_usulankoleksi
		$this->forge->dropTable('t_usulankoleksi', true);
		$this->forge->addField([
			'id' 			=> ['type' => 'MEDIUMINT','constraint' => '11','unsigned' => true,'auto_increment' => true,],
			'Title' 		=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'Author' 		=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'PublishYear' 	=> ['type' => 'VARCHAR','constraint' => '50','null' => true,],
			'Publisher' 	=> ['type' => 'VARCHAR','constraint' => '50','null' => true,],
			'description' 	=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'slug' 			=> ['type' => 'VARCHAR','constraint' => '150','null' => true,],
			'isbn'		 	=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'Status'		=> ['type' => 'VARCHAR','constraint' => '20','null' => true,],
			'created_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'updated_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'created_at' 	=> ['type' => 'DATETIME','null' => true,],
			'updated_at' 	=> ['type' => 'DATETIME','null' => true,],
			'deleted_at' 	=> ['type' => 'DATETIME','null' => true,],

		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_usulankoleksi');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_usulankoleksi', true);
	}
}
