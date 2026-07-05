<?php
// app/Core/helpers.php

// --- Output ---

function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// --- Routing ---

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function query_string(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    return http_build_query($params);
}

// --- Rendering ---

function render(string $view, array $data = [], string $layout = 'layouts/main'): void
{
    extract($data);
    ob_start();
    require __DIR__ . '/../Views/' . $view . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/../Views/' . $layout . '.php';
}

function partial(string $name, array $data = []): void
{
    extract($data);
    require __DIR__ . '/../Views/partials/' . $name . '.php';
}

// --- Flash messages ---

function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (empty($_SESSION['flash'][$key])) return null;
    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
}

// --- Old input ---

function old(string $field, mixed $default = ''): string
{
    return e((string)($_SESSION['old'][$field] ?? $default));
}

function set_old(array $input): void
{
    $_SESSION['old'] = $input;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

// --- Auth guard ---

function require_login(): void
{
    $app = require __DIR__ . '/../../config/app.php';

    if (empty($_SESSION['user_id'])) {
        flash('error', 'Vui long dang nhap de tiep tuc.');
        redirect('/login');
    }

    $timeout = $app['session_timeout'];
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        $_SESSION = [];
        session_destroy();
        flash('error', 'Phien dang nhap da het han. Vui long dang nhap lai.');
        redirect('/login');
    }
    $_SESSION['last_activity'] = time();
}

function require_admin(): void
{
    require_login();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        render('errors/403', ['title' => '403 Forbidden']);
        exit;
    }
}

function is_admin(): bool
{
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

// --- CSRF ---

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Logging ---

function log_error(string $message, ?Throwable $e = null): void
{
    $logFile = __DIR__ . '/../../storage/logs/app.log';
    $ts      = date('Y-m-d H:i:s');
    $line    = "[{$ts}] ERROR: {$message}";
    if ($e !== null) {
        $line .= ' | ' . get_class($e) . ': ' . $e->getMessage();
        $line .= ' in ' . $e->getFile() . ':' . $e->getLine();
    }
    $line .= PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

function log_info(string $message): void
{
    $logFile = __DIR__ . '/../../storage/logs/app.log';
    $ts      = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[{$ts}] INFO: {$message}" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// --- Misc ---

function number_vnd(float $amount): string
{
    return number_format($amount, 0, '.', ',') . ' d';
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
