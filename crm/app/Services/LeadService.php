<?php
// app/Services/LeadService.php

class LeadService
{
    private const STATUSES = ['new','contacted','qualified','lost'];
    private const SOURCES  = ['facebook','google','zalo','walk-in','referral','tiktok','website','other'];

    public function __construct(private LeadRepository $repo) {}

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
            'leads'      => $this->repo->getPaginated($keyword, $perPage, $offset, $sort, $direction, $status, $dateFrom, $dateTo),
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
            'name'            => trim($input['name'] ?? ''),
            'email'           => trim($input['email'] ?? ''),
            'phone'           => trim($input['phone'] ?? ''),
            'course_interest' => trim($input['course_interest'] ?? ''),
            'status'          => trim($input['status'] ?? 'new'),
            'note'            => trim($input['note'] ?? ''),
            'source'          => trim($input['source'] ?? ''),
        ];

        if ($values['name'] === '')  $errors['name']  = 'Ho ten hoc vien khong duoc de trong.';
        if ($values['email'] === '') {
            $errors['email'] = 'Email khong duoc de trong.';
        } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email khong dung dinh dang.';
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

        try {
            $id = $this->repo->create($v['values']);
            return ['success' => true, 'id' => $id];
        } catch (DuplicateRecordException $e) {
            return ['success' => false, 'errors' => ['email' => $e->getMessage()]];
        }
    }

    public function update(int $id, array $input): array
    {
        if (!$this->repo->findById($id)) {
            return ['success' => false, 'errors' => ['general' => 'Lead khong ton tai hoac da bi xoa.']];
        }
        $v = $this->validate($input);
        if (!empty($v['errors'])) return ['success' => false, 'errors' => $v['errors']];

        try {
            $this->repo->update($id, $v['values']);
            return ['success' => true];
        } catch (DuplicateRecordException $e) {
            return ['success' => false, 'errors' => ['email' => $e->getMessage()]];
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

    public function statuses(): array { return self::STATUSES; }
    public function sources(): array  { return self::SOURCES; }
}
