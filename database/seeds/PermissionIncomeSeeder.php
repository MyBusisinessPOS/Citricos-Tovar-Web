<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionIncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate(['name' => 'income_add']);
        Permission::updateOrCreate(['name' => 'income_delete']);
        Permission::updateOrCreate(['name' => 'income_edit']);
        Permission::updateOrCreate(['name' => 'income_view']);

        // $permissions = Permission::where('name', 'LIKE', 'income_%')->get();
    }
}
