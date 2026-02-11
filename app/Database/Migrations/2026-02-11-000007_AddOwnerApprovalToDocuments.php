<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOwnerApprovalToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'owner_approval_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'approver_id',
            ],
        ];

        $this->forge->addColumn('documents', $fields);

        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('ALTER TABLE `documents` ADD CONSTRAINT `documents_owner_approval_id_foreign` FOREIGN KEY (`owner_approval_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT');
        }
    }

    public function down()
    {
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('ALTER TABLE `documents` DROP FOREIGN KEY `documents_owner_approval_id_foreign`');
        }
        $this->forge->dropColumn('documents', 'owner_approval_id');
    }
}
