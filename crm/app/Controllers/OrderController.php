<?php
// app/Controllers/OrderController.php

class OrderController
{
    public function __construct(private OrderService $service) {}

    public function index(): void
    {
        require_login();
        $data = $this->service->getList($_GET);
        render('orders/index', ['title' => 'Quản lý Order'] + $data);
    }

    public function create(): void
    {
        require_login();
        render('orders/create', [
            'title'      => 'Thêm Order mới',
            'errors'     => [],
            'old'        => [],
            'statuses'   => $this->service->statuses(),
            'payMethods' => $this->service->payMethods(),
        ]);
    }

    public function store(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phiên làm việc hết hạn.');
            redirect('/orders/create');
        }

        $result = $this->service->create($_POST);

        if (!$result['success']) {
            set_old($_POST);
            render('orders/create', [
                'title'      => 'Thêm Order mới',
                'errors'     => $result['errors'],
                'old'        => $_POST,
                'statuses'   => $this->service->statuses(),
                'payMethods' => $this->service->payMethods(),
            ]);
            return;
        }

        clear_old();
        flash('success', 'Order đã được tạo thành công!');
        redirect('/orders');
    }

    public function edit(): void
    {
        require_login();
        $id    = (int)($_GET['id'] ?? 0);
        $order = $this->service->find($id);

        if (!$order) {
            http_response_code(404);
            render('errors/404', ['title' => '404']);
            return;
        }

        render('orders/edit', [
            'title'      => 'Sửa Order #' . $id,
            'order'      => $order,
            'errors'     => [],
            'old'        => $order,
            'statuses'   => $this->service->statuses(),
            'payMethods' => $this->service->payMethods(),
        ]);
    }

    public function update(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phiên làm việc hết hạn.');
            redirect('/orders');
        }

        $id     = (int)($_POST['id'] ?? 0);
        $result = $this->service->update($id, $_POST);

        if (!$result['success']) {
            $order = $this->service->find($id);
            render('orders/edit', [
                'title'      => 'Sửa Order #' . $id,
                'order'      => $order ?? [],
                'errors'     => $result['errors'],
                'old'        => $_POST,
                'statuses'   => $this->service->statuses(),
                'payMethods' => $this->service->payMethods(),
            ]);
            return;
        }

        flash('success', 'Cập nhật order thành công!');
        redirect('/orders');
    }

    public function delete(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phiên làm việc hết hạn.');
            redirect('/orders');
        }

        // Bonus: chỉ admin được xóa, staff chỉ create/update
        if (!is_admin()) {
            flash('error', 'Bạn không có quyền xóa order. Vui lòng liên hệ admin.');
            redirect('/orders');
        }

        $id = (int)($_POST['id'] ?? 0);
        $this->service->delete($id);
        flash('success', 'Đã xóa order thành công (soft delete).');
        redirect('/orders');
    }
}
