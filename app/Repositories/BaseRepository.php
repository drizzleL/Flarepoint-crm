<?php
namespace App\Repositories;

class BaseRepository
{
    protected $model;
    public function setTenantId($id = null)
    {
        if (!is_null($id)) {
            $this->model = $this->model->where('tenant_id', $id);
        }
    }
    public function returnModel()
    {
        return $this->model;
    }
}
