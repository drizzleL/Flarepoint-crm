<?php
namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\BaseRepository;
use App\Repositories\TenantTrait;

class DepartmentRepository extends BaseRepository
    implements DepartmentRepositoryContract
{
    use TenantTrait;

    public function __construct(Department $department)
    {
        $this->model = $department;
    }

    public function getAllDepartments()
    {
        return $this->model->get();
    }

    public function listAllDepartments()
    {
        return $this->model->pluck('name', 'id');
    }

    public function create($requestData = null)
    {
        if (is_null($requestData)) {
            return $this->model->create(['name' => 'Management']);
        }
        return $this->model->create($requestData->all());
    }

    public function destroy($id)
    {
        $this->model->findorFail($id)->delete();
    }

    public function getFirstDepartment()
    {
        return $this->model->first();
    }
}
