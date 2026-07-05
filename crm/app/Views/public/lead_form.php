<div class="card form-card" style="margin-top:60px">
    <h1>Đăng ký tư vấn miễn phí</h1>
    <p style="color:#64748b;font-size:14px">Để lại thông tin, đội ngũ tư vấn viên sẽ liên hệ với bạn trong 24h.</p>

    <?php if (!empty($errors['general'])): ?>
        <p class="error-text"><?= e($errors['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="/public-leads">
        <?= csrf_field() ?>

        <!-- Honeypot: field ẩn, người dùng thật không thấy nên sẽ để trống; bot tự động điền vào -->
        <div class="honeypot-field" aria-hidden="true">
            <label for="website">Website (để trống)</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <label>Họ tên</label>
        <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>">
        <?php if (!empty($errors['name'])): ?><p class="error-text"><?= e($errors['name']) ?></p><?php endif; ?>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?><p class="error-text"><?= e($errors['email']) ?></p><?php endif; ?>

        <label>Số điện thoại</label>
        <input type="text" name="phone" value="<?= e($old['phone'] ?? '') ?>">

        <label>Khóa học quan tâm</label>
        <input type="text" name="course_interest" value="<?= e($old['course_interest'] ?? '') ?>" placeholder="VD: PHP Web Development">

        <label>Ghi chú (tuỳ chọn)</label>
        <textarea name="note" rows="3"><?= e($old['note'] ?? '') ?></textarea>

        <button type="submit" class="btn primary">Đăng ký ngay</button>
    </form>
</div>
