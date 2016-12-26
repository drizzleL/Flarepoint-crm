<?php
namespace App\Repositories;

class BaseRepository
{
    protected $model;
    public function belongsToTenant($id)
    {
        $this->model = $this->model->where('tenant_id', $id);
    }
    public function returnModel()
    {
        return $this->model;
    }

    protected function cloneModel()
    {
        return clone $this->model;
    }
}
