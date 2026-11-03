<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vektor embedding katalog untuk pencarian semantik OPAC (diisi oleh `php spark semantic:index`).
 * Embedding = 384 float32 little-endian (1536 byte), sudah dinormalisasi L2.
 */
class CreateCatalogEmbeddings extends Migration
{
    protected $DBGroup = 'data';

    public function up()
    {
        $this->forge->addField([
            'Catalog_id' => [
                'type' => 'INT',
            ],
            'Embedding' => [
                'type' => 'BLOB',
            ],
            'ContentHash' => [
                'type' => 'CHAR',
                'constraint' => 32,
            ],
            'UpdatedAt' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addPrimaryKey('Catalog_id');
        $this->forge->addKey('UpdatedAt');
        $this->forge->createTable('catalog_embeddings', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('catalog_embeddings', true);
    }
}
