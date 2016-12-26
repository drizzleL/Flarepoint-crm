<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Tenant\TenantRepository;
use App\Repositories\User\UserRepository;
use App\Services\RegisterService;

class RegisterController extends Controller
{
    public function __construct(RegisterService $service)
    {
        $this->service = $service;
        //$this->tenants = $tenants;
        //$this->users = $users;
    }
    public function showRegistrationForm()
    {
        return view('auth.register');

    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users|max:20',
            'password' => 'required|confirmed',
            'tenant' => 'required',
        ]);
        $this->service->registerTenant($request);
        //$user = $this->users->create($request);
        //$tenant = $this->tenants->create($request->tenant, $user->id);
        //$user->tenant_id = $tenant->id;
        //$user->save();
        return redirect('/');
    }
}
