<?php
namespace App\Repositories\Tenant;

use App\Models\Tenant;
use Session;

class TenantRepository implements TenantRepositoryContract
{
    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }

    public function create($tenant_name, $id)
    {
        if ($this->checkDuplicatedTenant($tenant_name, $id)) {
            //return;
        }
        $tenant = $this->model;
        $tenant->name = $tenant_name;
        $tenant->creator_id = $id;
        $tenant->owner_id = $id;
        $tenant->save();

        //Session::flash('flash_message', 'User successfully added!'); //Snippet in Master.blade.php
        return $tenant;
    }

    public function checkDuplicatedTenant($tenant_name, $id)
    {
        return $this->model->where('creator_id', $id)
            ->where('name', $tenant_name)->exists();
    }

}
