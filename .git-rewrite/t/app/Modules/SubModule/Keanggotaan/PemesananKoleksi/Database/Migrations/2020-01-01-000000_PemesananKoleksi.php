<?php

namespace PemesananKoleksi\Database\Migrations;

use CodeIgniter\Database\Migration;

class PemesananKoleksi extends Migration
{
	public function up()
	{
		// t_pemesanankoleksi
		$this->forge->dropTable('t_pemesanankoleksi', true);
		$this->forge->addField([
			'id' 			=> ['type' => 'MEDIUMINT','constraint' => '11','unsigned' => true,'auto_increment' => true,],
			'catalog_id' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'eksemplar_id' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'description' 	=> ['type' => 'VARCHAR','constraint' => '255','null' => true,],
			'slug' 			=> ['type' => 'VARCHAR','constraint' => '150','null' => true,],
			'created_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'updated_by' 	=> ['type' => 'INT','constraint' => 11,'null' => true,],
			'created_at' 	=> ['type' => 'DATETIME','null' => true,],
			'updated_at' 	=> ['type' => 'DATETIME','null' => true,],
			'deleted_at' 	=> ['type' => 'DATETIME','null' => true,],

		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('t_pemesanankoleksi');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('t_pemesanankoleksi', true);
	}
}
