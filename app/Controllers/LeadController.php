<?php
// app/Controllers/LeadController.php

class LeadController
{
    public function __construct(private LeadService $service) {}

    public function index(): void
    {
        require_login();
        $data = $this->service->getList($_GET);
        render('leads/index', ['title' => 'Quan ly Lead'] + $data);
    }

    public function create(): void
    {
        require_login();
        render('leads/create', [
            'title'    => 'Them Lead moi',
            'errors'   => [],
            'old'      => [],
            'statuses' => $this->service->statuses(),
            'sources'  => $this->service->sources(),
        ]);
    }

    public function store(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phien lam viec het han.');
            redirect('/leads/create');
        }

        $result = $this->service->create($_POST);

        if (!$result['success']) {
            set_old($_POST);
            render('leads/create', [
                'title'    => 'Them Lead moi',
                'errors'   => $result['errors'],
                'old'      => $_POST,
                'statuses' => $this->service->statuses(),
                'sources'  => $this->service->sources(),
            ]);
            return;
        }

        clear_old();
        flash('success', 'Lead da duoc them thanh cong!');
        redirect('/leads');
    }

    public function edit(): void
    {
        require_login();
        $id   = (int)($_GET['id'] ?? 0);
        $lead = $this->service->find($id);

        if (!$lead) {
            http_response_code(404);
            render('errors/404', ['title' => '404']);
            return;
        }

        render('leads/edit', [
            'title'    => 'Sua Lead #' . $id,
            'lead'     => $lead,
            'errors'   => [],
            'old'      => $lead,
            'statuses' => $this->service->statuses(),
            'sources'  => $this->service->sources(),
        ]);
    }

    public function update(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phien lam viec het han.');
            redirect('/leads');
        }

        $id     = (int)($_POST['id'] ?? 0);
        $result = $this->service->update($id, $_POST);

        if (!$result['success']) {
            $lead = $this->service->find($id);
            render('leads/edit', [
                'title'    => 'Sua Lead #' . $id,
                'lead'     => $lead ?? [],
                'errors'   => $result['errors'],
                'old'      => $_POST,
                'statuses' => $this->service->statuses(),
                'sources'  => $this->service->sources(),
            ]);
            return;
        }

        flash('success', 'Cap nhat lead thanh cong!');
        redirect('/leads');
    }

    public function delete(): void
    {
        require_login();

        if (!csrf_verify()) {
            flash('error', 'Phien lam viec het han.');
            redirect('/leads');
        }

        // Bonus: chi admin duoc xoa
        if (!is_admin()) {
            flash('error', 'Ban khong co quyen xoa lead.');
            redirect('/leads');
        }

        $id = (int)($_POST['id'] ?? 0);
        $this->service->delete($id);
        flash('success', 'Da xoa lead thanh cong (soft delete).');
        redirect('/leads');
    }
}
