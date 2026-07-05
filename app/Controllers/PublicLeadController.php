<?php
// app/Controllers/PublicLeadController.php
// Form công khai (không cần login) để khách tự đăng ký tư vấn.

class PublicLeadController
{
    public function __construct(private LeadService $service) {}

    public function create(): void
    {
        render('public/lead_form', [
            'title'    => 'Đăng ký tư vấn — Training Center',
            'errors'   => [],
            'old'      => [],
            'statuses' => $this->service->statuses(),
        ], 'layouts/public'); // layout riêng không có navbar, vì form công khai
    }

    public function store(): void
    {
        // ── Honeypot ──────────────────────────────────────────────
        // Field 'website' ẩn bằng CSS, bot tự động điền vào nhưng người dùng thật thì không thấy.
        if (!empty($_POST['website'])) {
            log_info('Honeypot triggered, IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            // Giả vờ thành công để không "dạy" cho bot biết bị chặn
            flash('success', 'Cảm ơn bạn đã đăng ký, chúng tôi sẽ liên hệ sớm!');
            redirect('/public-leads/create');
        }

        // ── Rate limit theo session: tối thiểu 5 giây giữa 2 lần submit ──
        $now      = time();
        $lastSubmit = $_SESSION['last_public_lead_submit'] ?? 0;
        if (($now - $lastSubmit) < 5) {
            render('public/lead_form', [
                'title'    => 'Đăng ký tư vấn',
                'errors'   => ['general' => 'Bạn thao tác quá nhanh, vui lòng thử lại sau giây lát.'],
                'old'      => $_POST,
                'statuses' => $this->service->statuses(),
            ], 'layouts/public');
            return;
        }

        if (!csrf_verify()) {
            render('public/lead_form', [
                'title'    => 'Đăng ký tư vấn',
                'errors'   => ['general' => 'Phiên làm việc hết hạn, vui lòng thử lại.'],
                'old'      => $_POST,
                'statuses' => $this->service->statuses(),
            ], 'layouts/public');
            return;
        }

        // Form công khai mặc định status = new, source = website
        $input = $_POST;
        $input['status'] = 'new';
        $input['source'] = $input['source'] ?: 'website';

        $result = $this->service->create($input);

        if (!$result['success']) {
            render('public/lead_form', [
                'title'    => 'Đăng ký tư vấn',
                'errors'   => $result['errors'],
                'old'      => $_POST,
                'statuses' => $this->service->statuses(),
            ], 'layouts/public');
            return;
        }

        $_SESSION['last_public_lead_submit'] = $now;

        // PRG: redirect để F5 không tạo lead trùng
        flash('success', 'Đăng ký thành công! Đội ngũ tư vấn sẽ liên hệ với bạn sớm nhất.');
        redirect('/public-leads/create');
    }
}
