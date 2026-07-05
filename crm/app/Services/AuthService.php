<?php
// app/Services/AuthService.php

class AuthService
{
    public function __construct(private UserRepository $repo) {}

    public function attemptLogin(string $email, string $password): array
    {
        $email = trim($email);

        if ($email === '' || $password === '') {
            return ['success' => false, 'error' => 'Vui long nhap email va mat khau.'];
        }

        $user = $this->repo->findActiveByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            log_info("Login failed for email: {$email}");
            return ['success' => false, 'error' => 'Email hoac mat khau khong dung.'];
        }

        // Regenerate session ID de chong session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_role']     = $user['role'];
        $_SESSION['last_activity'] = time();

        $this->repo->updateLastLogin($user['id']);
        log_info("Login success: {$email} (role={$user['role']})");

        return ['success' => true];
    }

    public function logout(): void
    {
        log_info("Logout: user_id=" . ($_SESSION['user_id'] ?? 'unknown'));

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
