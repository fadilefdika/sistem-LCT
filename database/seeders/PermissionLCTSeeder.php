<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionLCTSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'create_lct_report',
            'approve_lct_report',
            'manage_lct_report',
            'approve_correction',
            'create_to_do_list',
            'manage_expenses',
            'approve_expenses',
            'monitor_progress',
            'complete_task',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}

