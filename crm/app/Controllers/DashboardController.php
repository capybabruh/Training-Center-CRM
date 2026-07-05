<?php
// app/Controllers/DashboardController.php

class DashboardController
{
    public function __construct(
        private LeadRepository  $leadRepo,
        private OrderRepository $orderRepo
    ) {}

    public function index(): void
    {
        require_login();

        $leadStats  = $this->leadRepo->countByStatus();
        $orderStats = $this->orderRepo->countByStatus();

        render('dashboard/index', [
            'title'         => 'Dashboard - Training Center CRM',
            'leadStats'     => $leadStats,
            'orderStats'    => $orderStats,
            'totalRevenue'  => $this->orderRepo->totalRevenue(),
            'monthRevenue'  => $this->orderRepo->revenueThisMonth(),
            'newLeadsMonth' => $this->leadRepo->countNewThisMonth(),
            'totalLeads'    => array_sum($leadStats),
            'totalOrders'   => array_sum($orderStats),
        ]);
    }
}
