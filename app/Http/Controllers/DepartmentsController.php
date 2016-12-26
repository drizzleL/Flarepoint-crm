<?php
namespace App\Http\Controllers;

use Session;
use App\Http\Requests;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Repositories\Department\DepartmentRepositoryContract;

class DepartmentsController extends Controller
{

    protected $departments;

    public function __construct(DepartmentRepositoryContract $departments)
    {
        $this->departments = $departments;
        $this->middleware('user.is.admin', ['only' => ['create', 'destroy']]);
    }
    public function index()
    {
        $this->departments->belongsToTenant(auth()->user()->tenant_id);
        return view('departments.index')
        ->withDepartment($this->departments->getAllDepartments());
    }
    public function create()
    {
        return view('departments.create');
    }
    public function store(StoreDepartmentRequest $request)
    {
        $department = $this->departments->create($request);
        $department->tenant_id = auth()->user()->tenant_id;
        $department->save();
        Session::flash('flash_message', 'Successfully created New Department');
        return redirect()->route('departments.index');
    }
    public function destroy($id)
    {
        $this->departments->destroy($id);
        return redirect()->route('departments.index');
    }
}
