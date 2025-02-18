<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;   
use Illuminate\Database\Seeder;
use App\Models\RoleLCT;

class RoleLCTSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'user', 'guard_name' => 'web'],
            ['name' => 'ehs', 'guard_name' => 'web'],
            ['name' => 'pic', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            $roleInstance = RoleLCT::firstOrCreate(['name' => $role['name']], $role);
            // Assign permissions to roles
            $permissions = $this->getPermissionsForRole($role['name']);
            $roleInstance->syncPermissions($permissions);
        }
    }

    // Menentukan permissions untuk setiap role
    private function getPermissionsForRole($role)
    {
        switch ($role) {
            case 'user':
                return ['create_lct_report'];
            case 'ehs':
                return ['approve_lct_report', 'manage_lct_report', 'approve_correction', 'monitor_progress'];
            case 'pic':
                return ['create_to_do_list', 'manage_expenses', 'approve_expenses', 'complete_task'];
            case 'manager':
                return ['manage_lct_report', 'approve_expenses'];
            default:
                return [];
        }
    }
}
