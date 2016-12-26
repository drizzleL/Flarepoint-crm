<?php
namespace App\Repositories;

trait TenantTrait
{
    function setTenantId($id)
    {
        $model = $this->model;
        $model->tenant_id = $id;
        $model->save();
    }
    function whereTenant($id)
    {
        $model = $this->model;
        $model = $model->where('tenant_id', $id);
        $this->model = $model;
    }
}
