<?php
namespace App\Repositories\Role;

use App\Models\Role;
use App\Models\Permissions;
use App\Repositories\BaseRepostiroy;

class RoleRepository extends BaseRepostiroy implements RoleRepositoryContract
{
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function listAllRoles()
    {
        $model = $this->cloneModel();
        return $model->pluck('name', 'id');
    }

    public function allPermissions()
    {
        return Permissions::all();
    }

    public function allRoles()
    {
        $model = $this->cloneModel();
        return $model->get();
    }

    public function permissionsUpdate($requestData)
    {
        $allowed_permissions = [];

        if ($requestData->input('permissions') != null) {
            foreach ($requestData->input('permissions')
            as $permissionId => $permission) {
                if ($permission === '1') {
                    $allowed_permissions[] = (int)$permissionId;
                }
            }
        } else {
            $allowed_permissions = [];
        }

        $role = Role::find($requestData->input('role_id'));

        $role->permissions()->sync($allowed_permissions);
        $role->save();
    }

    public function create($requestData)
    {
        $roleName = $requestData->name;
        $roleDescription = $requestData->description;
        Role::create([
            'name' => strtolower($roleName),
            'display_name' => ucfirst($roleName),
             'description' => $roleDescription
             ]);
    }

    public function destroy($id)
    {
        $role = Role::findorFail($id);
        if ($role->id !== 1) {
            $role->delete();
        } else {
            Session()->flash('flash_message_warning', 'Can not delete Administrator role');
        }
    }
}
