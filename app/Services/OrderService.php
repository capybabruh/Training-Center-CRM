<?php
// app/Services/OrderService.php

class OrderService
{
    private const STATUSES = ['pending','paid','partial','cancelled'];
    private const PAY_METHODS = ['cash','bank_transfer','card'];

    public function __construct(private OrderRepository $repo) {}

    // --- List ---

    public function getList(array $query): array
    {
        $keyword  = trim($query['q'] ?? '');
        $status   = $query['status'] ?? '';
        $dateFrom = $query['date_from'] ?? '';
        $dateTo   = $query['date_to']   ?? '';
        $sort      = $query['sort']      ?? 'created_at';
        $direction = $query['direction'] ?? 'desc';
        $page      = max(1, (int)($query['page'] ?? 1));
        $perPage   = 10;

        if (!in_array($status, array_merge([''], self::STATUSES), true)) $status = '';

        $total      = $this->repo->countAll($keyword, $status, $dateFrom, $dateTo);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        return [
            'orders'     => $this->repo->getPaginated($keyword, $perPage, $offset, $sort, $direction, $status, $dateFrom, $dateTo),
            'keyword'    => $keyword,
            'status'     => $status,
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
            'sort'       => $sort,
            'direction'  => $direction,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
            'statuses'   => self::STATUSES,
        ];
    }

    // --- Validation ---

    private function validate(array $input): array
    {
        $errors = [];
        $values = [
            'order_code'     => trim($input['order_code'] ?? ''),
            'customer_name'  => trim($input['customer_name'] ?? ''),
            'customer_email' => trim($input['customer_email'] ?? ''),
            'course_name'    => trim($input['course_name'] ?? ''),
            'total_amount'   => trim($input['total_amount'] ?? ''),
            'paid_amount'    => trim($input['paid_amount'] ?? '0'),
            'status'         => trim($input['status'] ?? 'pending'),
            'note'           => trim($input['note'] ?? ''),
        ];

        if ($values['order_code'] === '')    $errors['order_code']    = 'Ma don hang khong duoc de trong.';
        if ($values['customer_name'] === '') $errors['customer_name'] = 'Ten khach hang khong duoc de trong.';
        if ($values['course_name'] === '')   $errors['course_name']   = 'Ten khoa hoc khong duoc de trong.';

        if ($values['total_amount'] === '' || !is_numeric($values['total_amount'])) {
            $errors['total_amount'] = 'Hoc phi phai la so hop le.';
        } elseif ((float)$values['total_amount'] < 0) {
            $errors['total_amount'] = 'Hoc phi khong duoc am.';
        } else {
            $values['total_amount'] = (float)$values['total_amount'];
        }

        $values['paid_amount'] = max(0, (float)$values['paid_amount']);
        if (isset($values['total_amount']) && is_float($values['total_amount']) && $values['paid_amount'] > $values['total_amount']) {
            $errors['paid_amount'] = 'So tien da thanh toan khong duoc lon hon tong hoc phi.';
        }

        if ($values['customer_email'] !== '' && !filter_var($values['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['customer_email'] = 'Email khach hang khong dung dinh dang.';
        }

        if (!in_array($values['status'], self::STATUSES, true)) {
            $errors['status'] = 'Trang thai khong hop le.';
        }

        return ['errors' => $errors, 'values' => $values];
    }

    // --- CRUD ---

    public function create(array $input): array
    {
        $v = $this->validate($input);
        if (!empty($v['errors'])) return ['success' => false, 'errors' => $v['errors']];

        $payment = null;
        $paidAmt = $v['values']['paid_amount'];
        if ($paidAmt > 0) {
            $payment = [
                'amount' => $paidAmt,
                'method' => $input['pay_method'] ?? 'cash',
                'note'   => 'Thanh toan khi tao don',
            ];
        }

        try {
            $id = $this->repo->createWithPayment($v['values'], $payment);
            return ['success' => true, 'id' => $id];
        } catch (DuplicateRecordException $e) {
            return ['success' => false, 'errors' => ['order_code' => $e->getMessage()]];
        }
    }

    public function update(int $id, array $input): array
    {
        if (!$this->repo->findById($id)) {
            return ['success' => false, 'errors' => ['general' => 'Don hang khong ton tai hoac da bi xoa.']];
        }
        $v = $this->validate($input);
        if (!empty($v['errors'])) return ['success' => false, 'errors' => $v['errors']];

        try {
            $this->repo->update($id, $v['values']);
            return ['success' => true];
        } catch (DuplicateRecordException $e) {
            return ['success' => false, 'errors' => ['order_code' => $e->getMessage()]];
        }
    }

    public function delete(int $id): array
    {
        if ($id <= 0) return ['success' => false, 'errors' => ['general' => 'ID khong hop le.']];
        $this->repo->delete($id);
        return ['success' => true];
    }

    public function find(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function statuses(): array    { return self::STATUSES; }
    public function payMethods(): array  { return self::PAY_METHODS; }
}
