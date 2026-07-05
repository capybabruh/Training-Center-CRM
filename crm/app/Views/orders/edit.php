<h1>Sửa Order #<?= e((string)($order['id'] ?? '')) ?></h1>

<?php if (!empty($errors['general'])): ?>
    <p class="error-text"><?= e($errors['general']) ?></p>
<?php endif; ?>

<form method="post" action="/orders/update" class="card form-card">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string)$order['id']) ?>">

    <label>Mã đơn hàng</label>
    <input type="text" name="order_code" value="<?= e($old['order_code'] ?? $order['order_code']) ?>">
    <?php if (!empty($errors['order_code'])): ?><p class="error-text"><?= e($errors['order_code']) ?></p><?php endif; ?>

    <label>Tên khách hàng</label>
    <input type="text" name="customer_name" value="<?= e($old['customer_name'] ?? $order['customer_name']) ?>">
    <?php if (!empty($errors['customer_name'])): ?><p class="error-text"><?= e($errors['customer_name']) ?></p><?php endif; ?>

    <label>Email khách hàng</label>
    <input type="email" name="customer_email" value="<?= e($old['customer_email'] ?? $order['customer_email'] ?? '') ?>">
    <?php if (!empty($errors['customer_email'])): ?><p class="error-text"><?= e($errors['customer_email']) ?></p><?php endif; ?>

    <label>Khóa học</label>
    <input type="text" name="course_name" value="<?= e($old['course_name'] ?? $order['course_name']) ?>">
    <?php if (!empty($errors['course_name'])): ?><p class="error-text"><?= e($errors['course_name']) ?></p><?php endif; ?>

    <label>Tổng học phí (VNĐ)</label>
    <input type="number" name="total_amount" value="<?= e($old['total_amount'] ?? $order['total_amount']) ?>" min="0" step="1000">
    <?php if (!empty($errors['total_amount'])): ?><p class="error-text"><?= e($errors['total_amount']) ?></p><?php endif; ?>

    <label>Số tiền đã thanh toán</label>
    <input type="number" name="paid_amount" value="<?= e($old['paid_amount'] ?? $order['paid_amount']) ?>" min="0" step="1000">
    <?php if (!empty($errors['paid_amount'])): ?><p class="error-text"><?= e($errors['paid_amount']) ?></p><?php endif; ?>

    <label>Trạng thái</label>
    <select name="status">
        <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= ($old['status'] ?? $order['status']) === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errors['status'])): ?><p class="error-text"><?= e($errors['status']) ?></p><?php endif; ?>

    <label>Ghi chú</label>
    <textarea name="note" rows="3"><?= e($old['note'] ?? $order['note'] ?? '') ?></textarea>

    <div style="display:flex;gap:10px">
        <button type="submit" class="btn primary">Cập nhật</button>
        <a href="/orders" class="btn">Hủy</a>
    </div>
</form>

<?php if (!empty($order['payment_amounts'])): ?>
<div class="card">
    <h2>Lịch sử thanh toán</h2>
    <p>Các đợt thanh toán: <?= e($order['payment_amounts']) ?> VNĐ</p>
</div>
<?php endif; ?>
