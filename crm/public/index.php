<?php
// public/index.php — Front Controller
// Mọi request đều đi qua file này.

// ─── Session cookie setup (PHẢI chạy trước session_start()) ───────────────
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ─── Core ───────────────────────────────────────────────────────────────────
require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/DuplicateRecordException.php';

// ─── Repositories ─────────────────────────────────────────────────────────
require __DIR__ . '/../app/Repositories/UserRepository.php';
require __DIR__ . '/../app/Repositories/LeadRepository.php';
require __DIR__ . '/../app/Repositories/OrderRepository.php';

// ─── Services ─────────────────────────────────────────────────────────────
require __DIR__ . '/../app/Services/AuthService.php';
require __DIR__ . '/../app/Services/LeadService.php';
require __DIR__ . '/../app/Services/OrderService.php';

// ─── Controllers ──────────────────────────────────────────────────────────
require __DIR__ . '/../app/Controllers/HomeController.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/DashboardController.php';
require __DIR__ . '/../app/Controllers/LeadController.php';
require __DIR__ . '/../app/Controllers/OrderController.php';
require __DIR__ . '/../app/Controllers/PublicLeadController.php';
require __DIR__ . '/../app/Controllers/HealthController.php';

// ─── Error display theo môi trường (debug=false ở production) ─────────────
$appConfig = require __DIR__ . '/../config/app.php';
if ($appConfig['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// ─── Dependency wiring (đơn giản, không cần container phức tạp) ───────────
try {
    $dbConfig = require __DIR__ . '/../config/database.php';
    $pdo = Database::connect($dbConfig);
} catch (Throwable $e) {
    log_error('Database connection failed at bootstrap', $e);
    http_response_code(500);
    // Bootstrap chưa load đủ view system nên trả HTML tối giản, an toàn (không lộ SQLSTATE)
    echo '<h1>503 Service Unavailable</h1><p>Hệ thống tạm thời không khả dụng. Vui lòng thử lại sau.</p>';
    exit;
}

$userRepo  = new UserRepository($pdo);
$leadRepo  = new LeadRepository($pdo);
$orderRepo = new OrderRepository($pdo);

$authService  = new AuthService($userRepo);
$leadService  = new LeadService($leadRepo);
$orderService = new OrderService($orderRepo);

$container = [
    HomeController::class       => new HomeController(),
    AuthController::class       => new AuthController($authService),
    DashboardController::class  => new DashboardController($leadRepo, $orderRepo),
    LeadController::class       => new LeadController($leadService),
    OrderController::class      => new OrderController($orderService),
    PublicLeadController::class => new PublicLeadController($leadService),
    HealthController::class     => new HealthController(),
];

// ─── Route table ──────────────────────────────────────────────────────────
$router = new Router();

$router->get('/',        [HomeController::class, 'index']);
$router->get('/health',  [HealthController::class, 'index']);

// Auth
$router->get('/login',   [AuthController::class, 'login']);
$router->post('/login',  [AuthController::class, 'handleLogin']);
$router->post('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

// Public lead form (no login required)
$router->get('/public-leads/create', [PublicLeadController::class, 'create']);
$router->post('/public-leads',       [PublicLeadController::class, 'store']);

// Leads (module A)
$router->get('/leads',         [LeadController::class, 'index']);
$router->get('/leads/create',  [LeadController::class, 'create']);
$router->post('/leads/store',  [LeadController::class, 'store']);
$router->get('/leads/edit',    [LeadController::class, 'edit']);
$router->post('/leads/update', [LeadController::class, 'update']);
$router->post('/leads/delete', [LeadController::class, 'delete']);

// Orders (module B)
$router->get('/orders',         [OrderController::class, 'index']);
$router->get('/orders/create',  [OrderController::class, 'create']);
$router->post('/orders/store',  [OrderController::class, 'store']);
$router->get('/orders/edit',    [OrderController::class, 'edit']);
$router->post('/orders/update', [OrderController::class, 'update']);
$router->post('/orders/delete', [OrderController::class, 'delete']);

// ─── Dispatch ───────────────────────────────────────────────────────────────
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
