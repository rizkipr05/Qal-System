<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocuments extends Migration
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
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'doc_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'draft',
            ],
            'owner_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'reviewer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'approver_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'current_version_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'locked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('doc_number');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('owner_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('approver_id', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('documents', true);
    }

    public function down()
    {
        $this->forge->dropTable('documents', true);
    }
}
