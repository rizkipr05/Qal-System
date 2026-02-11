<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFileNameToDocumentVersions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('document_versions', [
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'file_path',
            ],
        ]);

        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query("UPDATE `document_versions` SET `file_name` = SUBSTRING_INDEX(`file_path`, '/', -1) WHERE `file_name` IS NULL AND `file_path` IS NOT NULL");
        }
    }

    public function down()
    {
        $this->forge->dropColumn('document_versions', 'file_name');
    }
}
