<h1>Quản lý Order</h1>
<a class="btn primary" href="/orders/create">+ Thêm Order</a>

<form method="get" action="/orders" class="toolbar">
    <input type="hidden" name="page" value="1">
    <input type="hidden" name="sort" value="<?= e($sort) ?>">
    <input type="hidden" name="direction" value="<?= e($direction) ?>">
    <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Tìm theo mã đơn, khách hàng, khóa học">

    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>

    <label style="font-weight:400">Từ: <input type="date" name="date_from" value="<?= e($date_from) ?>"></label>
    <label style="font-weight:400">Đến: <input type="date" name="date_to" value="<?= e($date_to) ?>"></label>

    <button type="submit" class="btn primary">Lọc</button>
    <a href="/orders" class="btn">Reset</a>
</form>

<table>
<thead>
<tr>
    <th>ID</th>
    <th><a href="?<?= e(query_string(['sort'=>'order_code','direction'=>($sort==='order_code'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Mã đơn <?= $sort==='order_code' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th><a href="?<?= e(query_string(['sort'=>'customer_name','direction'=>($sort==='customer_name'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Khách hàng <?= $sort==='customer_name' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th><a href="?<?= e(query_string(['sort'=>'course_name','direction'=>($sort==='course_name'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Khóa học <?= $sort==='course_name' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th><a href="?<?= e(query_string(['sort'=>'total_amount','direction'=>($sort==='total_amount'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Học phí <?= $sort==='total_amount' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th>Đã thu</th>
    <th><a href="?<?= e(query_string(['sort'=>'status','direction'=>($sort==='status'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Trạng thái <?= $sort==='status' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th><a href="?<?= e(query_string(['sort'=>'created_at','direction'=>($sort==='created_at'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Ngày tạo <?= $sort==='created_at' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($orders as $order): ?>
<tr>
    <td><?= e((string)$order['id']) ?></td>
    <td><?= e($order['order_code']) ?></td>
    <td><?= e($order['customer_name']) ?></td>
    <td><?= e($order['course_name']) ?></td>
    <td><?= number_vnd((float)$order['total_amount']) ?></td>
    <td><?= number_vnd((float)$order['paid_amount']) ?></td>
    <td><span class="badge badge-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></td>
    <td><?= e($order['created_at']) ?></td>
    <td>
        <a href="/orders/edit?id=<?= e((string)$order['id']) ?>">Sửa</a>
        <?php if (is_admin()): ?>
            <form method="post" action="/orders/delete" class="inline" onsubmit="return confirm('Xóa order này?')">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e((string)$order['id']) ?>">
                <button type="submit" class="link danger">Xóa</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($orders)): ?>
    <tr><td colspan="9" style="text-align:center;color:#94a3b8">Không có dữ liệu phù hợp.</td></tr>
<?php endif; ?>
</tbody>
</table>

<?php partial('pagination', compact('page', 'totalPages', 'total')); ?>
