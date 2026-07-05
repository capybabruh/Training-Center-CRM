<?php
// app/Repositories/OrderRepository.php

class OrderRepository
{
    public function __construct(private PDO $db) {}

    // --- Helpers ---

    private function buildWhere(string $keyword, string $status, string $dateFrom, string $dateTo): array
    {
        $where  = ['o.deleted_at IS NULL'];
        $params = [];

        if ($keyword !== '') {
            $where[]       = '(o.order_code LIKE :kw1 OR o.customer_name LIKE :kw2 OR o.customer_email LIKE :kw3 OR o.course_name LIKE :kw4)';
            $params['kw1'] = '%' . $keyword . '%';
            $params['kw2'] = '%' . $keyword . '%';
            $params['kw3'] = '%' . $keyword . '%';
            $params['kw4'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where[]          = 'o.status = :status';
            $params['status'] = $status;
        }
        if ($dateFrom !== '') {
            $where[]              = 'o.created_at >= :date_from';
            $params['date_from']  = $dateFrom . ' 00:00:00';
        }
        if ($dateTo !== '') {
            $where[]            = 'o.created_at <= :date_to';
            $params['date_to']  = $dateTo . ' 23:59:59';
        }

        return ['sql' => 'WHERE ' . implode(' AND ', $where), 'params' => $params];
    }

    // --- Read ---

    public function countAll(string $keyword = '', string $status = '', string $dateFrom = '', string $dateTo = ''): int
    {
        $w    = $this->buildWhere($keyword, $status, $dateFrom, $dateTo);
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM orders o {$w['sql']}");
        $stmt->execute($w['params']);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function getPaginated(
        string $keyword,
        int    $limit,
        int    $offset,
        string $sort      = 'created_at',
        string $direction = 'desc',
        string $status    = '',
        string $dateFrom  = '',
        string $dateTo    = ''
    ): array {
        $allowedSorts = ['id','order_code','customer_name','course_name','total_amount','status','created_at'];
        $allowedDirs  = ['asc','desc'];

        if (!in_array($sort, $allowedSorts, true))                  $sort      = 'created_at';
        if (!in_array(strtolower($direction), $allowedDirs, true))  $direction = 'desc';

        $w   = $this->buildWhere($keyword, $status, $dateFrom, $dateTo);
        $sql = "SELECT o.id, o.order_code, o.customer_name, o.customer_email,
                       o.course_name, o.total_amount, o.paid_amount, o.status, o.created_at
                FROM orders o {$w['sql']}
                ORDER BY o.{$sort} {$direction}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($w['params'] as $k => $v) {
            $stmt->bindValue(':' . $k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, GROUP_CONCAT(p.amount ORDER BY p.paid_at) AS payment_amounts
             FROM orders o
             LEFT JOIN order_payments p ON p.order_id = o.id
             WHERE o.id = :id AND o.deleted_at IS NULL
             GROUP BY o.id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getPayments(int $orderId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM order_payments WHERE order_id = :order_id ORDER BY paid_at DESC"
        );
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    // --- Write ---

    // Tao order + payment record trong 1 transaction (bonus)
    public function createWithPayment(array $order, ?array $payment): int
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO orders (order_code, customer_name, customer_email, course_name,
                                        total_amount, paid_amount, status, note)
                    VALUES (:order_code, :customer_name, :customer_email, :course_name,
                            :total_amount, :paid_amount, :status, :note)";
            try {
                $this->db->prepare($sql)->execute([
                    'order_code'     => $order['order_code'],
                    'customer_name'  => $order['customer_name'],
                    'customer_email' => $order['customer_email'] ?: null,
                    'course_name'    => $order['course_name'],
                    'total_amount'   => $order['total_amount'],
                    'paid_amount'    => $order['paid_amount'] ?? 0,
                    'status'         => $order['status'],
                    'note'           => $order['note'] ?: null,
                ]);
            } catch (PDOException $e) {
                if (($e->errorInfo[1] ?? 0) === 1062) {
                    throw new DuplicateRecordException('Ma don hang nay da ton tai trong he thong.');
                }
                throw $e;
            }
            $orderId = (int)$this->db->lastInsertId();

            if ($payment !== null && (float)$payment['amount'] > 0) {
                $this->db->prepare(
                    "INSERT INTO order_payments (order_id, amount, method, note, paid_at)
                     VALUES (:order_id, :amount, :method, :note, NOW())"
                )->execute([
                    'order_id' => $orderId,
                    'amount'   => $payment['amount'],
                    'method'   => $payment['method'] ?? 'cash',
                    'note'     => $payment['note'] ?? null,
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE orders SET order_code=:order_code, customer_name=:customer_name,
                customer_email=:customer_email, course_name=:course_name,
                total_amount=:total_amount, paid_amount=:paid_amount,
                status=:status, note=:note, updated_at=NOW()
                WHERE id=:id AND deleted_at IS NULL";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id'             => $id,
                'order_code'     => $data['order_code'],
                'customer_name'  => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?: null,
                'course_name'    => $data['course_name'],
                'total_amount'   => $data['total_amount'],
                'paid_amount'    => $data['paid_amount'] ?? 0,
                'status'         => $data['status'],
                'note'           => $data['note'] ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062) {
                throw new DuplicateRecordException('Ma don hang nay da ton tai trong he thong.');
            }
            throw $e;
        }
    }

    // Soft delete
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE orders SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute(['id' => $id]);
    }

    // --- Dashboard stats ---

    public function countByStatus(): array
    {
        $rows = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM orders WHERE deleted_at IS NULL GROUP BY status"
        )->fetchAll();
        $result = ['pending'=>0,'paid'=>0,'partial'=>0,'cancelled'=>0];
        foreach ($rows as $r) $result[$r['status']] = (int)$r['cnt'];
        return $result;
    }

    public function totalRevenue(): float
    {
        return (float)$this->db->query(
            "SELECT COALESCE(SUM(paid_amount),0) FROM orders WHERE deleted_at IS NULL AND status != 'cancelled'"
        )->fetchColumn();
    }

    public function revenueThisMonth(): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(paid_amount),0) FROM orders
             WHERE deleted_at IS NULL AND status != 'cancelled'
               AND YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())"
        );
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }
}
