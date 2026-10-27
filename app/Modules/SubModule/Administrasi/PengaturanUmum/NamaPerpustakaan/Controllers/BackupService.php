<?php

namespace NamaPerpustakaan\Controllers;

use mysqli;
use RuntimeException;
use ZipArchive;

class BackupService
{
    public function database(mysqli $connection, string $destination): void
    {
        $output = fopen($destination, 'wb');
        if ($output === false) {
            throw new RuntimeException('Tidak dapat membuat file SQL.');
        }
        try {
            $connection->set_charset('utf8mb4');
            $tables = [];
            $views = [];
            foreach ($connection->query('SHOW FULL TABLES')->fetch_all(MYSQLI_NUM) as [$name, $type]) {
                if ($type === 'VIEW') {
                    $views[] = $name;
                } else {
                    $tables[] = $name;
                }
            }
            $this->write($output, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");
            $connection->query('START TRANSACTION WITH CONSISTENT SNAPSHOT');
            try {
                foreach ($tables as $table) {
                    $quotedTable = $this->identifier($table);
                    $definition = $connection->query('SHOW CREATE TABLE ' . $quotedTable)->fetch_assoc()['Create Table'];
                    $this->write($output, 'DROP TABLE IF EXISTS ' . $quotedTable . ";\n" . $definition . ";\n\n");
                    $columns = [];
                    foreach ($connection->query('SHOW COLUMNS FROM ' . $quotedTable)->fetch_all(MYSQLI_ASSOC) as $field) {
                        if (stripos($field['Extra'], 'GENERATED') === false) {
                            $columns[] = $field['Field'];
                        }
                    }
                    if ($columns === []) {
                        continue;
                    }
                    $columnNames = implode(', ', array_map([$this, 'identifier'], $columns));
                    $rows = $connection->query('SELECT ' . $columnNames . ' FROM ' . $quotedTable, MYSQLI_USE_RESULT);
                    try {
                        $fields = $rows->fetch_fields();
                        while ($row = $rows->fetch_row()) {
                            $values = [];
                            foreach ($row as $index => $value) {
                                $values[] = $value === null ? 'NULL' :
                                    ((($fields[$index]->flags & MYSQLI_BINARY_FLAG) !== 0) ? '0x' . bin2hex($value) : "'" . $connection->real_escape_string($value) . "'");
                            }
                            $this->write($output, 'INSERT INTO ' . $quotedTable . ' (' . $columnNames . ') VALUES (' . implode(', ', $values) . ");\n");
                        }
                    } finally {
                        $rows->free();
                    }
                    $this->write($output, "\n");
                }
            } finally {
                $connection->query('ROLLBACK');
            }
            foreach (['PROCEDURE', 'FUNCTION'] as $kind) {
                $routines = $connection->query("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '" . $kind . "'")->fetch_all(MYSQLI_NUM);
                foreach ($routines as [$routine]) {
                    $quotedRoutine = $this->identifier($routine);
                    $definition = $connection->query('SHOW CREATE ' . $kind . ' ' . $quotedRoutine)->fetch_assoc()['Create ' . ucfirst(strtolower($kind))];
                    if (!is_string($definition) || $definition === '') {
                        throw new RuntimeException('Definisi routine tidak dapat dibaca.');
                    }
                    $this->write($output, 'DROP ' . $kind . ' IF EXISTS ' . $quotedRoutine . ";\nDELIMITER ;;\n" . $definition . ";;\nDELIMITER ;\n\n");
                }
            }
            foreach ($connection->query('SHOW EVENTS')->fetch_all(MYSQLI_ASSOC) as $event) {
                $quotedEvent = $this->identifier($event['Name']);
                $definition = $connection->query('SHOW CREATE EVENT ' . $quotedEvent)->fetch_assoc()['Create Event'];
                $this->write($output, 'DROP EVENT IF EXISTS ' . $quotedEvent . ";\nDELIMITER ;;\n" . $definition . ";;\nDELIMITER ;\n\n");
            }
            foreach ($views as $view) {
                $quotedView = $this->identifier($view);
                $definition = $connection->query('SHOW CREATE VIEW ' . $quotedView)->fetch_assoc()['Create View'];
                $this->write($output, 'DROP VIEW IF EXISTS ' . $quotedView . ";\n" . $definition . ";\n\n");
            }
            foreach ($connection->query('SHOW TRIGGERS')->fetch_all(MYSQLI_ASSOC) as $trigger) {
                $quotedTrigger = $this->identifier($trigger['Trigger']);
                $definition = $connection->query('SHOW CREATE TRIGGER ' . $quotedTrigger)->fetch_assoc()['SQL Original Statement'];
                $this->write($output, 'DROP TRIGGER IF EXISTS ' . $quotedTrigger . ";\nDELIMITER ;;\n" . $definition . ";;\nDELIMITER ;\n\n");
            }
            $this->write($output, "SET FOREIGN_KEY_CHECKS=1;\n");
        } finally {
            fclose($output);
        }
    }

    public function uploads(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            throw new RuntimeException('Folder uploads tidak ditemukan.');
        }
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP zip diperlukan.');
        }
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat file ZIP.');
        }
        try {
            $root = realpath($source);
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            $zip->addEmptyDir('uploads');
            foreach ($files as $file) {
                if ($file->isLink()) {
                    continue;
                }
                $entry = 'uploads/' . str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
                if ($file->isDir()) {
                    if (!$zip->addEmptyDir($entry)) {
                        throw new RuntimeException('Gagal menambahkan folder ke ZIP.');
                    }
                } elseif (!$file->isReadable() || !$zip->addFile($file->getPathname(), $entry)) {
                    throw new RuntimeException('Gagal menambahkan file ke ZIP.');
                }
            }
        } finally {
            if (!$zip->close()) {
                throw new RuntimeException('Gagal menyelesaikan file ZIP.');
            }
        }
    }

    private function identifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    private function write($output, string $text): void
    {
        $length = strlen($text);
        for ($offset = 0; $offset < $length; $offset += $written) {
            $written = fwrite($output, substr($text, $offset));
            if ($written === false || $written === 0) {
                throw new RuntimeException('Gagal menulis file backup.');
            }
        }
    }
}