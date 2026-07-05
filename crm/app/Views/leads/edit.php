<h1>Sửa Lead #<?= e((string)($lead['id'] ?? '')) ?></h1>

<?php if (!empty($errors['general'])): ?>
    <p class="error-text"><?= e($errors['general']) ?></p>
<?php endif; ?>

<form method="post" action="/leads/update" class="card form-card">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string)$lead['id']) ?>">

    <label>Họ tên</label>
    <input type="text" name="name" value="<?= e($old['name'] ?? $lead['name']) ?>">
    <?php if (!empty($errors['name'])): ?><p class="error-text"><?= e($errors['name']) ?></p><?php endif; ?>

    <label>Email</label>
    <input type="email" name="email" value="<?= e($old['email'] ?? $lead['email']) ?>">
    <?php if (!empty($errors['email'])): ?><p class="error-text"><?= e($errors['email']) ?></p><?php endif; ?>

    <label>Số điện thoại</label>
    <input type="text" name="phone" value="<?= e($old['phone'] ?? $lead['phone'] ?? '') ?>">

    <label>Khóa học quan tâm</label>
    <input type="text" name="course_interest" value="<?= e($old['course_interest'] ?? $lead['course_interest'] ?? '') ?>">

    <label>Trạng thái</label>
    <select name="status">
        <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= ($old['status'] ?? $lead['status']) === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errors['status'])): ?><p class="error-text"><?= e($errors['status']) ?></p><?php endif; ?>

    <label>Kênh tiếp cận</label>
    <select name="source">
        <option value="">-- Chọn --</option>
        <?php foreach ($sources as $src): ?>
            <option value="<?= e($src) ?>" <?= ($old['source'] ?? $lead['source'] ?? '') === $src ? 'selected' : '' ?>><?= e($src) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Ghi chú</label>
    <textarea name="note" rows="3"><?= e($old['note'] ?? $lead['note'] ?? '') ?></textarea>

    <div style="display:flex;gap:10px">
        <button type="submit" class="btn primary">Cập nhật</button>
        <a href="/leads" class="btn">Hủy</a>
    </div>
</form>
