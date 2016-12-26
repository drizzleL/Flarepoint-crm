<?php

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createTenant = new Tenant;
        $createTenant->name = 'Tenant1';
        $createTenant->creator_id = 1;
        $createTenant->owner_id = 1;
        $createTenant->save();

        $createTenant = new Tenant;
        $createTenant->name = 'Tenant2';
        $createTenant->creator_id = 2;
        $createTenant->owner_id = 2;
        $createTenant->save();
    }
}
