<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DcSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'  => 'Drafter User',
                'email' => 'drafter@example.com',
                'password' => password_hash('drafter123', PASSWORD_BCRYPT),
                'role'  => 'drafter',
            ],
            [
                'name'  => 'Reviewer User',
                'email' => 'reviewer@example.com',
                'password' => password_hash('reviewer123', PASSWORD_BCRYPT),
                'role'  => 'reviewer',
            ],
            [
                'name'  => 'Approver User',
                'email' => 'approver@example.com',
                'password' => password_hash('approver123', PASSWORD_BCRYPT),
                'role'  => 'approver',
            ],
            [
                'name'  => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role'  => 'admin',
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
