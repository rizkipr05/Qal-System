<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $helpers = ['form', 'url'];

    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index()
    {
        $currentUser = $this->currentUser();
        $list = $this->users->orderBy('name', 'ASC')->findAll();

        return view('admin/users', [
            'currentUser' => $currentUser,
            'users'       => $list,
        ]);
    }

    public function create()
    {
        $currentUser = $this->currentUser();
        return view('admin/create_user', [
            'currentUser' => $currentUser,
        ]);
    }

    public function store()
    {
        $currentUser = $this->currentUser();

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'role'     => $this->request->getPost('role'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];

        $id = $this->users->insert($data, true);
        $this->logActivity($currentUser['id'], 'admin_create_user', ['user_id' => $id]);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $currentUser = $this->currentUser();
        $user = $this->users->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        return view('admin/edit_user', [
            'currentUser' => $currentUser,
            'user'        => $user,
        ]);
    }

    public function update(int $id)
    {
        $currentUser = $this->currentUser();
        $user = $this->users->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role'),
        ];

        $this->users->update($id, $data);
        $this->logActivity($currentUser['id'], 'admin_update_user', ['user_id' => $id]);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil diupdate.');
    }

    public function resetPassword(int $id)
    {
        $currentUser = $this->currentUser();
        $newPassword = $this->request->getPost('password');

        $this->users->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        $this->logActivity($currentUser['id'], 'admin_reset_password', ['user_id' => $id]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Password berhasil direset.');
    }

    public function logs()
    {
        $currentUser = $this->currentUser();
        $logs = (new ActivityLogModel())
            ->select('activity_logs.*, users.name as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->findAll(200);

        return view('admin/logs', [
            'currentUser' => $currentUser,
            'logs'        => $logs,
        ]);
    }

    private function currentUser(): array
    {
        $session = session();
        $userId = $session->get('user_id');
        if (!$userId) {
            redirect()->to(site_url('login'))->send();
            exit;
        }

        $user = $this->users->find($userId);
        if (!$user) {
            session()->destroy();
            redirect()->to(site_url('login'))->send();
            exit;
        }

        if ($user['role'] !== 'admin') {
            redirect()->to(site_url('dc'))->send();
            exit;
        }

        return $user;
    }
}
