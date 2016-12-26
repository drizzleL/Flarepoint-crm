<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $currentUserTenantId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                $this->currentUserTenantId = auth()->user()->tenant_id;
            }
            return $next($request);
        });
    }
}
