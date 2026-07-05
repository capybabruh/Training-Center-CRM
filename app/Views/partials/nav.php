<nav class="navbar">
    <div class="navbar-brand">
        <a href="/dashboard">Training Center CRM</a>
    </div>
    <div class="navbar-links">
        <a href="/dashboard">Dashboard</a>
        <a href="/leads">Leads</a>
        <a href="/orders">Orders</a>
        <a href="/health" target="_blank">Health</a>
    </div>
    <div class="navbar-user">
        <span>
            <?= e($_SESSION['user_name'] ?? '') ?>
            <small class="badge badge-<?= e($_SESSION['user_role'] ?? '') ?>"><?= e($_SESSION['user_role'] ?? '') ?></small>
        </span>
        <form method="post" action="/logout" class="inline">
            <?= csrf_field() ?>
            <button type="submit" class="link">Đăng xuất</button>
        </form>
    </div>
</nav>
