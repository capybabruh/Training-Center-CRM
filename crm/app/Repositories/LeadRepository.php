<?php
// app/Repositories/LeadRepository.php

class LeadRepository
{
    public function __construct(private PDO $db) {}

    // --- Helpers ---

    private function buildWhere(string $keyword, string $status, string $dateFrom, string $dateTo): array
    {
        $where  = ['deleted_at IS NULL'];
        $params = [];

        if ($keyword !== '') {
            $where[]       = '(name LIKE :kw1 OR email LIKE :kw2 OR phone LIKE :kw3)';
            $params['kw1'] = '%' . $keyword . '%';
            $params['kw2'] = '%' . $keyword . '%';
            $params['kw3'] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where[]          = 'status = :status';
            $params['status'] = $status;
        }
        if ($dateFrom !== '') {
            $where[]              = 'created_at >= :date_from';
            $params['date_from']  = $dateFrom . ' 00:00:00';
        }
        if ($dateTo !== '') {
            $where[]            = 'created_at <= :date_to';
            $params['date_to']  = $dateTo . ' 23:59:59';
        }

        return ['sql' => 'WHERE ' . implode(' AND ', $where), 'params' => $params];
    }

    // --- Read ---

    public function countAll(string $keyword = '', string $status = '', string $dateFrom = '', string $dateTo = ''): int
    {
        $w    = $this->buildWhere($keyword, $status, $dateFrom, $dateTo);
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM leads {$w['sql']}");
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
        $allowedSorts = ['id','name','email','status','course_interest','created_at'];
        $allowedDirs  = ['asc','desc'];

        if (!in_array($sort, $allowedSorts, true))                  $sort      = 'created_at';
        if (!in_array(strtolower($direction), $allowedDirs, true))  $direction = 'desc';

        $w    = $this->buildWhere($keyword, $status, $dateFrom, $dateTo);
        $sql  = "SELECT id, name, email, phone, course_interest, status, source, created_at
                 FROM leads {$w['sql']}
                 ORDER BY {$sort} {$direction}
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
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // --- Write ---

    public function create(array $data): int
    {
        $sql = "INSERT INTO leads (name, email, phone, course_interest, status, note, source)
                VALUES (:name, :email, :phone, :course_interest, :status, :note, :source)";
        try {
            $this->db->prepare($sql)->execute([
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'] ?: null,
                'course_interest' => $data['course_interest'] ?: null,
                'status'          => $data['status'],
                'note'            => $data['note'] ?: null,
                'source'          => $data['source'] ?: null,
            ]);
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062) {
                throw new DuplicateRecordException('Email hoc vien nay da ton tai trong he thong.');
            }
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE leads SET name=:name, email=:email, phone=:phone,
                course_interest=:course_interest, status=:status, note=:note,
                source=:source, updated_at=NOW()
                WHERE id=:id AND deleted_at IS NULL";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id'              => $id,
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'] ?: null,
                'course_interest' => $data['course_interest'] ?: null,
                'status'          => $data['status'],
                'note'            => $data['note'] ?: null,
                'source'          => $data['source'] ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062) {
                throw new DuplicateRecordException('Email hoc vien nay da ton tai trong he thong.');
            }
            throw $e;
        }
    }

    // Soft delete
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE leads SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute(['id' => $id]);
    }

    // --- Dashboard stats ---

    public function countByStatus(): array
    {
        $rows = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM leads WHERE deleted_at IS NULL GROUP BY status"
        )->fetchAll();
        $result = ['new' => 0, 'contacted' => 0, 'qualified' => 0, 'lost' => 0];
        foreach ($rows as $r) {
            $result[$r['status']] = (int)$r['cnt'];
        }
        return $result;
    }

    public function countNewThisMonth(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM leads
             WHERE deleted_at IS NULL
               AND YEAR(created_at)  = YEAR(NOW())
               AND MONTH(created_at) = MONTH(NOW())"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
