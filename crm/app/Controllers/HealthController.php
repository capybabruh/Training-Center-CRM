<?php
// app/Controllers/HealthController.php

class HealthController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $config = require __DIR__ . '/../../config/database.php';
            $pdo    = Database::connect($config);
            $pdo->query('SELECT 1');

            echo json_encode([
                'app'      => 'Training Center CRM',
                'status'   => 'ok',
                'database' => 'connected',
                'time'     => date('c'),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            log_error('HealthController DB check failed', $e);
            http_response_code(500);
            echo json_encode([
                'app'      => 'Training Center CRM',
                'status'   => 'error',
                'database' => 'disconnected',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}
