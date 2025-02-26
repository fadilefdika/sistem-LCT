<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignDefaultRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Authenticated $event)
    {
        $user = $event->user;

        $existingRole = DB::table('lct_user_roles')->where('model_id', $user->id)->first();

        if (!$existingRole) {
            $defaultRole = DB::table('roles_lct')->where('name', 'user')->first();

            if ($defaultRole) {
                DB::table('lct_user_roles')->insert([
                    'model_id' => $user->id,
                    'role_id' => $defaultRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
