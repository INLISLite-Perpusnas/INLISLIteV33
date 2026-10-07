<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerpusKelilingTables extends Migration
{
    public function up()
    {
        // 1. perpus_keliling_unit
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nomor_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nomor_kendaraan');
        $this->forge->addKey('status');
        $this->forge->createTable('perpus_keliling_unit', true);

        // 2. perpus_keliling_unit_foto
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'urutan' => [
                'type'       => 'SMALLINT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'is_utama' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['unit_id', 'urutan']);
        $this->forge->addForeignKey('unit_id', 'perpus_keliling_unit', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('perpus_keliling_unit_foto', true);

        // 3. perpus_keliling_lokasi
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->createTable('perpus_keliling_lokasi', true);

        // 4. perpus_keliling_petugas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tampilkan_email' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'tampilkan_hp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('perpus_keliling_petugas', true);

        // 5. perpus_keliling_jadwal
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'lokasi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jam_mulai' => [
                'type' => 'TIME',
            ],
            'jam_selesai' => [
                'type' => 'TIME',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'dibatalkan'],
                'default'    => 'aktif',
            ],
            'alasan_batal' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dibatalkan_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'dibatalkan_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['tanggal', 'status']);
        $this->forge->addKey(['unit_id', 'tanggal']);
        $this->forge->addKey('lokasi_id');
        $this->forge->addForeignKey('unit_id', 'perpus_keliling_unit', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('lokasi_id', 'perpus_keliling_lokasi', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('perpus_keliling_jadwal', true);

        // 6. perpus_keliling_jadwal_petugas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jadwal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['jadwal_id', 'petugas_id']);
        $this->forge->addKey('petugas_id');
        $this->forge->addForeignKey('jadwal_id', 'perpus_keliling_jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('petugas_id', 'perpus_keliling_petugas', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('perpus_keliling_jadwal_petugas', true);

        // 7. perpus_keliling_dokumentasi
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jadwal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('jadwal_id');
        $this->forge->addForeignKey('jadwal_id', 'perpus_keliling_jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('perpus_keliling_dokumentasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('perpus_keliling_dokumentasi', true);
        $this->forge->dropTable('perpus_keliling_jadwal_petugas', true);
        $this->forge->dropTable('perpus_keliling_jadwal', true);
        $this->forge->dropTable('perpus_keliling_petugas', true);
        $this->forge->dropTable('perpus_keliling_lokasi', true);
        $this->forge->dropTable('perpus_keliling_unit_foto', true);
        $this->forge->dropTable('perpus_keliling_unit', true);
    }
}
