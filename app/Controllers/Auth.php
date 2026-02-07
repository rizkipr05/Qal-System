<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to(site_url('dc'));
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = (new UserModel())->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->to(site_url('login'))->with('error', 'Email atau password salah.');
        }

        session()->set('user_id', $user['id']);
        session()->set('user_role', $user['role']);

        return redirect()->to(site_url('dc'))->with('success', 'Login berhasil.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'Logout berhasil.');
    }
}
