<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Tenant\TenantRepository;
use App\Repositories\User\UserRepository;

class RegisterController extends Controller
{
    public function __construct(TenantRepository $tenants, UserRepository $users)
    {
        parent::__construct();
        $this->tenants = $tenants;
        $this->users = $users;
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
        $user = $this->users->create($request);
        $tenant = $this->tenants->create($request->tenant, $user->id);
        $user->tenant_id = $tenant->id;
        $user->save();
        return 'Tenant Created!';
    }
}
