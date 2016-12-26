<?php
namespace App\Services;

use DB;
use App\Repositories\User\UserRepository;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Tenant\TenantRepository;

class RegisterService
{
    public function __construct(UserRepository $user,
        DepartmentRepository $department, TenantRepository $tenant)
    {
        $this->users = $user;
        $this->departments = $department;
        $this->tenants = $tenant;
    }

    public function registerTenant($request)
    {
        DB::transaction(function () use ($request) {
            $user = $this->users->create($request);
            $tenant = $this->tenants->create($request->tenant, $user->id);
            $user->tenant_id = $tenant->id;
            $user->save();
            $department = $this->departments->create();
            $department->tenant_id = $tenant->id;
            $department->save();
            $user->department()->attach($department->id);
        });
    }
}
