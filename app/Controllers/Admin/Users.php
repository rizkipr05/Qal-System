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
        $allUsers = $this->users->orderBy('name', 'ASC')->findAll();
        $list = [];
        $manageableUserIds = [];
        foreach ($allUsers as $user) {
            if ($this->isAdmin($currentUser) || $this->canManageTargetUser($currentUser, $user)) {
                $list[] = $user;
            }

            if ($this->canManageTargetUser($currentUser, $user)) {
                $manageableUserIds[] = (int) $user['id'];
            }
        }

        return view('admin/users', [
            'currentUser' => $currentUser,
            'users'       => $list,
            'manageableUserIds' => $manageableUserIds,
            'canCreateUser' => $this->isAdmin($currentUser) || $this->isPc($currentUser),
        ]);
    }

    public function create()
    {
        $currentUser = $this->currentUser();

        return view('admin/create_user', [
            'currentUser' => $currentUser,
            'roleOptions' => $this->roleOptionsForActor($currentUser),
        ]);
    }

    public function store()
    {
        $currentUser = $this->currentUser();
        $role = $this->normalizeRole((string) $this->request->getPost('role'));
        $allowedRoles = array_keys($this->roleOptionsForActor($currentUser));
        if (!in_array($role, $allowedRoles, true)) {
            return redirect()->back()->withInput()->with('error', 'Role tidak diizinkan.');
        }

        $email = (string) $this->request->getPost('email');
        $exists = $this->users->where('email', $email)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $email,
            'role'     => $role,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];

        $id = $this->users->insert($data, true);
        $this->logActivity($currentUser['id'], 'create_user', ['user_id' => $id, 'by_role' => $this->normalizeRole($currentUser['role'])]);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $currentUser = $this->currentUser();
        $user = $this->users->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }
        if (!$this->canManageTargetUser($currentUser, $user)) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Anda tidak bisa mengedit user ini.');
        }

        return view('admin/edit_user', [
            'currentUser' => $currentUser,
            'user'        => $user,
            'roleOptions' => $this->roleOptionsForActor($currentUser),
        ]);
    }

    public function update(int $id)
    {
        $currentUser = $this->currentUser();
        $user = $this->users->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }
        if (!$this->canManageTargetUser($currentUser, $user)) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Anda tidak bisa mengedit user ini.');
        }

        $role = $this->normalizeRole((string) $this->request->getPost('role'));
        $allowedRoles = array_keys($this->roleOptionsForActor($currentUser));
        if (!in_array($role, $allowedRoles, true)) {
            return redirect()->back()->withInput()->with('error', 'Role tidak diizinkan.');
        }

        $email = (string) $this->request->getPost('email');
        $exists = $this->users->where('email', $email)->where('id !=', $id)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $email,
            'role'  => $role,
        ];

        $this->users->update($id, $data);
        $this->logActivity($currentUser['id'], 'update_user', ['user_id' => $id, 'by_role' => $this->normalizeRole($currentUser['role'])]);

        return redirect()->to(site_url('admin/users'))->with('success', 'User berhasil diupdate.');
    }

    public function resetPassword(int $id)
    {
        $currentUser = $this->currentUser();
        $target = $this->users->find($id);
        if (!$target) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User tidak ditemukan.');
        }
        if (!$this->canManageTargetUser($currentUser, $target)) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Anda tidak bisa reset password user ini.');
        }

        $newPassword = $this->request->getPost('password');

        $this->users->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        $this->logActivity($currentUser['id'], 'reset_password_user', ['user_id' => $id, 'by_role' => $this->normalizeRole($currentUser['role'])]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Password berhasil direset.');
    }

    public function logs()
    {
        $currentUser = $this->currentUser();
        if (!$this->isAdmin($currentUser)) {
            return redirect()->to(site_url('dc'))->with('error', 'Hanya admin yang bisa melihat activity logs.');
        }

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

        if (!in_array($this->normalizeRole((string) $user['role']), ['admin', 'pc'], true)) {
            redirect()->to(site_url('dc'))->send();
            exit;
        }

        return $user;
    }

    private function roleOptionsForActor(array $actor): array
    {
        if ($this->isAdmin($actor)) {
            return [
                'construction' => 'Construction',
                'qc' => 'Quality Control (QC)',
                'pc' => 'Project Control (PC)',
                'owner' => 'Owner',
                'admin' => 'Admin',
            ];
        }

        return [
            'construction' => 'Construction',
            'qc' => 'Quality Control (QC)',
            'owner' => 'Owner',
        ];
    }

    private function canManageTargetUser(array $actor, array $target): bool
    {
        if ($this->isAdmin($actor)) {
            return true;
        }

        if (!$this->isPc($actor)) {
            return false;
        }

        return in_array($this->normalizeRole((string) $target['role']), ['construction', 'qc', 'owner'], true);
    }

    private function isAdmin(array $user): bool
    {
        return $this->normalizeRole((string) $user['role']) === 'admin';
    }

    private function isPc(array $user): bool
    {
        return $this->normalizeRole((string) $user['role']) === 'pc';
    }

    private function normalizeRole(string $role): string
    {
        return match ($role) {
            'drafter' => 'construction',
            'reviewer' => 'qc',
            'approver' => 'pc',
            default => $role,
        };
    }
}
