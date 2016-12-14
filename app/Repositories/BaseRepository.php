<?php
namespace App\Repositories;

class BaseRepository
{
    protected $model;
    public function whereModelTenantId($id)
    {
        $this->model = $this->model->where('tenant_id', $id);
    }
    public function returnModel()
    {
        return $this->model;
    }
}
