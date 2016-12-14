<?php
namespace App\Repositories\Tenant;

interface TenantRepositoryContract
{

    public function create($tenant_name, $id);
}
