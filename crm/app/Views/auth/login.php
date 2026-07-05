<div class="card form-card" style="margin: 60px auto;">
    <h1>Đăng nhập</h1>
    <p style="color:#64748b;font-size:14px">Training Center CRM — hệ thống quản lý nội bộ</p>

    <?php if (!empty($errors['general'])): ?>
        <p class="error-text"><?= e($errors['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <?= csrf_field() ?>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autofocus>

        <label>Mật khẩu</label>
        <input type="password" name="password" required>

        <label style="display:flex;align-items:center;gap:8px;font-weight:400">
            <input type="checkbox" name="remember" style="width:auto">
            Ghi nhớ đăng nhập
        </label>
        <p style="font-size:12px;color:#94a3b8;margin-top:-6px">
            Lưu ý: tính năng này chỉ mang tính minh họa giao diện — hệ thống không lưu mật khẩu vào cookie
            vì lý do bảo mật. Triển khai thật cần dùng remember-token riêng lưu trong DB (xem Problem Solving câu 7).
        </p>

        <button type="submit" class="btn primary">Đăng nhập</button>
    </form>

    <div class="card" style="margin-top:18px;background:#f8fafc;font-size:13px">
        <strong>Tài khoản demo:</strong><br>
        Admin: admin@crm.local / password<br>
        Staff: an@crm.local / password
    </div>
</div>
