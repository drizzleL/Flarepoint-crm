<?php
namespace App\Services;

class DashboardService
{
    public function __construct()
    {
        $this->tenantId = $tenant_id;
    }

    public function getClientsCount($tenant_id)
    {
        return $this->clients->getCount();
    }

    public function getTasksCompletedThisMonth($tenant_id)
    {
    }

    public function getLeadsCompletedThisMonth($tenant_id)
    {
    }

    public function getUsers()
    {
        return $this->clients->getUsers();
    }
}
