<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentVersions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'document_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'revision' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['document_id', 'revision']);
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('document_versions', true);
    }

    public function down()
    {
        $this->forge->dropTable('document_versions', true);
    }
}
