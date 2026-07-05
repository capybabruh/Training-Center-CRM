<?php
// app/Controllers/AuthController.php

class AuthController
{
    public function __construct(private AuthService $service) {}

    public function login(): void
    {
        if (is_logged_in()) redirect('/dashboard');
        render('auth/login', ['title' => 'Dang nhap - Training Center CRM', 'errors' => [], 'old' => []]);
    }

    public function handleLogin(): void
    {
        if (!csrf_verify()) {
            render('auth/login', [
                'title'  => 'Dang nhap',
                'errors' => ['general' => 'Phien lam viec het han, vui long thu lai.'],
                'old'    => ['email' => $_POST['email'] ?? ''],
            ]);
            return;
        }

        $result = $this->service->attemptLogin(
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        if (!$result['success']) {
            render('auth/login', [
                'title'  => 'Dang nhap',
                'errors' => ['general' => $result['error']],
                'old'    => ['email' => $_POST['email'] ?? ''],
            ]);
            return;
        }

        flash('success', 'Dang nhap thanh cong. Chao mung, ' . e($_SESSION['user_name']) . '!');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        if (!csrf_verify()) redirect('/dashboard');
        $this->service->logout();
        flash('success', 'Ban da dang xuat thanh cong.');
        redirect('/login');
    }
}
