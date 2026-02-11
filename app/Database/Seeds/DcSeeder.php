<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DcSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'  => 'Construction User',
                'email' => 'construction@example.com',
                'password' => password_hash('construction', PASSWORD_BCRYPT),
                'role'  => 'construction',
            ],
            [
                'name'  => 'QC User',
                'email' => 'qc@example.com',
                'password' => password_hash('qc', PASSWORD_BCRYPT),
                'role'  => 'qc',
            ],
            [
                'name'  => 'PC User',
                'email' => 'pc@example.com',
                'password' => password_hash('pc', PASSWORD_BCRYPT),
                'role'  => 'pc',
            ],
            [
                'name'  => 'Owner User',
                'email' => 'owner@example.com',
                'password' => password_hash('owner', PASSWORD_BCRYPT),
                'role'  => 'owner',
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('users')->where('email', $user['email'])->update($user);
            } else {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
