<h1>Quản lý Lead</h1>
<a class="btn primary" href="/leads/create">+ Thêm Lead</a>

<form method="get" action="/leads" class="toolbar">
    <input type="hidden" name="page" value="1">
    <input type="hidden" name="sort" value="<?= e($sort) ?>">
    <input type="hidden" name="direction" value="<?= e($direction) ?>">
    <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Tìm theo tên, email, SĐT">

    <select name="status">
        <option value="">-- Tất cả trạng thái --</option>
        <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
    </select>

    <label style="font-weight:400">Từ: <input type="date" name="date_from" value="<?= e($date_from) ?>"></label>
    <label style="font-weight:400">Đến: <input type="date" name="date_to" value="<?= e($date_to) ?>"></label>

    <button type="submit" class="btn primary">Lọc</button>
    <a href="/leads" class="btn">Reset</a>
</form>

<table>
<thead>
<tr>
    <th>ID</th>
    <th><a href="?<?= e(query_string(['sort'=>'name','direction'=>($sort==='name'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Họ tên <?= $sort==='name' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th><a href="?<?= e(query_string(['sort'=>'email','direction'=>($sort==='email'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Email <?= $sort==='email' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
    <th>Phone</th>
    <th><a href="?<?= e(query_string(['sort'=>'course_interest','direction'=>($sort==='course_interest'&&$direction==='asc')?'desc':'asc','page'=>1])) ?>">
        Khóa quan tâm <?= $sort==='course_interest' ? ($direction==='asc'?'▲':'▼') : '' ?>
    </a></th>
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
<?php foreach ($leads as $lead): ?>
<tr>
    <td><?= e((string)$lead['id']) ?></td>
    <td><?= e($lead['name']) ?></td>
    <td><?= e($lead['email']) ?></td>
    <td><?= e($lead['phone'] ?? '') ?></td>
    <td><?= e($lead['course_interest'] ?? '') ?></td>
    <td><span class="badge badge-<?= e($lead['status']) ?>"><?= e($lead['status']) ?></span></td>
    <td><?= e($lead['created_at']) ?></td>
    <td>
        <a href="/leads/edit?id=<?= e((string)$lead['id']) ?>">Sửa</a>
        <?php if (is_admin()): ?>
            <form method="post" action="/leads/delete" class="inline" onsubmit="return confirm('Xóa lead này?')">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e((string)$lead['id']) ?>">
                <button type="submit" class="link danger">Xóa</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($leads)): ?>
    <tr><td colspan="8" style="text-align:center;color:#94a3b8">Không có dữ liệu phù hợp.</td></tr>
<?php endif; ?>
</tbody>
</table>

<?php partial('pagination', compact('page', 'totalPages', 'total')); ?>
