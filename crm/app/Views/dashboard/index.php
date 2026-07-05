<h1>Dashboard</h1>
<p style="color:#64748b">Xin chào, <?= e($_SESSION['user_name']) ?>! Đây là tổng quan hệ thống.</p>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value"><?= e((string)$newLeadsMonth) ?></div>
        <div class="stat-label">Lead mới tháng này</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= e((string)$totalLeads) ?></div>
        <div class="stat-label">Tổng số Lead</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= e((string)$totalOrders) ?></div>
        <div class="stat-label">Tổng số Order</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= number_vnd($monthRevenue) ?></div>
        <div class="stat-label">Doanh thu tháng này</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= number_vnd($totalRevenue) ?></div>
        <div class="stat-label">Tổng doanh thu (đã thu)</div>
    </div>
</div>

<div class="card">
    <h2>Lead theo trạng thái</h2>
    <table>
        <thead><tr><th>Trạng thái</th><th>Số lượng</th></tr></thead>
        <tbody>
        <?php foreach ($leadStats as $status => $count): ?>
            <tr>
                <td><span class="badge badge-<?= e($status) ?>"><?= e($status) ?></span></td>
                <td><?= e((string)$count) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Order theo trạng thái</h2>
    <table>
        <thead><tr><th>Trạng thái</th><th>Số lượng</th></tr></thead>
        <tbody>
        <?php foreach ($orderStats as $status => $count): ?>
            <tr>
                <td><span class="badge badge-<?= e($status) ?>"><?= e($status) ?></span></td>
                <td><?= e((string)$count) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Trạng thái hệ thống</h2>
    <p>Kiểm tra nhanh: <a href="/health" target="_blank">GET /health</a> (JSON kiểm tra kết nối DB)</p>
    <p>Vai trò của bạn: <span class="badge badge-<?= e($_SESSION['user_role']) ?>"><?= e($_SESSION['user_role']) ?></span>
    <?php if (!is_admin()): ?>
        <br><small style="color:#64748b">Tài khoản staff chỉ được tạo/sửa dữ liệu, không được xóa. Liên hệ admin nếu cần xóa.</small>
    <?php endif; ?>
    </p>
</div>
