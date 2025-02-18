<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat roles
        $roleUser = Role::create(['name' => 'user']);
        $roleEhs = Role::create(['name' => 'ehs']);
        $rolePic = Role::create(['name' => 'pic']);
        $roleManager = Role::create(['name' => 'manager']);

        // Permissions untuk User
        Permission::create(['name' => 'create report']); // Membuat laporan temuan

        // Permissions untuk EHS
        Permission::create(['name' => 'review user report']); // Melihat laporan user
        Permission::create(['name' => 'create ehs report']); // Membuat laporan EHS untuk PIC
        Permission::create(['name' => 'approve repair report']); // Approve laporan perbaikan PIC
        Permission::create(['name' => 'reject repair report']); // Reject laporan perbaikan PIC
        Permission::create(['name' => 'monitor progress']); // Monitoring pekerjaan PIC
        Permission::create(['name' => 'approve task completion']); // Approve jika semua task sudah selesai

        // Permissions untuk PIC
        Permission::create(['name' => 'view ehs report']); // Menerima laporan dari EHS
        Permission::create(['name' => 'submit repair report']); // Mengirim laporan perbaikan ke EHS
        Permission::create(['name' => 'create repair tasks']); // Membuat daftar task perbaikan
        Permission::create(['name' => 'update task status']); // Mengupdate status task

        // Permissions untuk Manajer
        Permission::create(['name' => 'monitor all processes']); // Monitoring seluruh sistem
        Permission::create(['name' => 'approve repair budget']); // Approve anggaran perbaikan jika medium/high

        // Assign permissions ke role
        $roleUser->givePermissionTo(['create report']);

        $roleEhs->givePermissionTo([
            'review user report', 'create ehs report', 
            'approve repair report', 'reject repair report', 
            'monitor progress', 'approve task completion'
        ]);

        $rolePic->givePermissionTo([
            'view ehs report', 'submit repair report', 
            'create repair tasks', 'update task status'
        ]);

        $roleManager->givePermissionTo([
            'monitor all processes', 'approve repair budget'
        ]);
    }
}
