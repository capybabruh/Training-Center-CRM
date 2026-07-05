<h1>Thêm Order mới</h1>

<?php if (!empty($errors['general'])): ?>
    <p class="error-text"><?= e($errors['general']) ?></p>
<?php endif; ?>

<form method="post" action="/orders/store" class="card form-card">
    <?= csrf_field() ?>

    <label>Mã đơn hàng</label>
    <input type="text" name="order_code" value="<?= e($old['order_code'] ?? '') ?>" placeholder="VD: ORD-2026-0001">
    <?php if (!empty($errors['order_code'])): ?><p class="error-text"><?= e($errors['order_code']) ?></p><?php endif; ?>

    <label>Tên khách hàng</label>
    <input type="text" name="customer_name" value="<?= e($old['customer_name'] ?? '') ?>">
    <?php if (!empty($errors['customer_name'])): ?><p class="error-text"><?= e($errors['customer_name']) ?></p><?php endif; ?>

    <label>Email khách hàng</label>
    <input type="email" name="customer_email" value="<?= e($old['customer_email'] ?? '') ?>">
    <?php if (!empty($errors['customer_email'])): ?><p class="error-text"><?= e($errors['customer_email']) ?></p><?php endif; ?>

    <label>Khóa học</label>
    <input type="text" name="course_name" value="<?= e($old['course_name'] ?? '') ?>">
    <?php if (!empty($errors['course_name'])): ?><p class="error-text"><?= e($errors['course_name']) ?></p><?php endif; ?>

    <label>Tổng học phí (VNĐ)</label>
    <input type="number" name="total_amount" value="<?= e($old['total_amount'] ?? '') ?>" min="0" step="1000">
    <?php if (!empty($errors['total_amount'])): ?><p class="error-text"><?= e($errors['total_amount']) ?></p><?php endif; ?>

    <label>Số tiền đã thanh toán (nếu có)</label>
    <input type="number" name="paid_amount" value="<?= e($old['paid_amount'] ?? '0') ?>" min="0" step="1000">
    <?php if (!empty($errors['paid_amount'])): ?><p class="error-text"><?= e($errors['paid_amount']) ?></p><?php endif; ?>
    <small style="color:#64748b">Nếu nhập &gt; 0, hệ thống sẽ tự tạo 1 payment record gắn với order này trong cùng 1 transaction.</small>

    <label>Phương thức thanh toán</label>
    <select name="pay_method">
        <?php foreach ($payMethods as $m): ?>
            <option value="<?= e($m) ?>"><?= e($m) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Trạng thái</label>
    <select name="status">
        <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= ($old['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errors['status'])): ?><p class="error-text"><?= e($errors['status']) ?></p><?php endif; ?>

    <label>Ghi chú</label>
    <textarea name="note" rows="3"><?= e($old['note'] ?? '') ?></textarea>

    <div style="display:flex;gap:10px">
        <button type="submit" class="btn primary">Lưu Order</button>
        <a href="/orders" class="btn">Hủy</a>
    </div>
</form>
