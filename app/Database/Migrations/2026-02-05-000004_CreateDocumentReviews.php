<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentReviews extends Migration
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
            'reviewer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'comment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('document_id');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('document_reviews', true);
    }

    public function down()
    {
        $this->forge->dropTable('document_reviews', true);
    }
}
